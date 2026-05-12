<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Client;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\PackagingMovement;
use App\Models\Drinks\Sale;
use App\Models\Drinks\SalePackagingLine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('caissier peut enregistrer un retour emballage', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 10]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'status' => TransactionStatus::Validated,
        'validated_by' => $caissier->id,
        'document_date' => today()->toDateString(),
        'created_by' => $caissier->id,
    ]);
    $packagingLine = SalePackagingLine::factory()->create([
        'sale_id' => $sale->id,
        'packaging_id' => $packaging->id,
        'quantity_out' => 5,
        'quantity_returned' => 0,
    ]);

    $this->actingAs($caissier)
        ->post(route('drinks.sale-packaging-lines.deconsign', [
            'current_team' => $team->slug,
            'salePackagingLine' => $packagingLine->id,
        ]), ['quantity_returned' => 3])
        ->assertRedirect();

    $packagingLine->refresh();
    expect($packagingLine->quantity_returned)->toBe(3);
});

test('retour emballage crée un mouvement ConsignmentReturn', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'status' => TransactionStatus::Validated,
        'validated_by' => $admin->id,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
    ]);
    $packagingLine = SalePackagingLine::factory()->create([
        'sale_id' => $sale->id,
        'packaging_id' => $packaging->id,
        'quantity_out' => 10,
        'quantity_returned' => 0,
    ]);

    $this->actingAs($admin)
        ->post(route('drinks.sale-packaging-lines.deconsign', [
            'current_team' => $team->slug,
            'salePackagingLine' => $packagingLine->id,
        ]), ['quantity_returned' => 4]);

    expect($packaging->fresh()->stock_qty)->toBe(4);

    $movement = PackagingMovement::where('packaging_id', $packaging->id)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->kind)->toBe(StockMovementKind::ConsignmentReturn)
        ->and($movement->quantity)->toBe(4);
});

test('retours multiples s\'accumulent sur quantity_returned', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'status' => TransactionStatus::Validated,
        'validated_by' => $admin->id,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
    ]);
    $packagingLine = SalePackagingLine::factory()->create([
        'sale_id' => $sale->id,
        'packaging_id' => $packaging->id,
        'quantity_out' => 10,
        'quantity_returned' => 2,
    ]);

    $this->actingAs($admin)
        ->post(route('drinks.sale-packaging-lines.deconsign', [
            'current_team' => $team->slug,
            'salePackagingLine' => $packagingLine->id,
        ]), ['quantity_returned' => 3]);

    expect($packagingLine->fresh()->quantity_returned)->toBe(5);
});

test('magasinier ne peut pas enregistrer un retour emballage', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);
    [$admin] = drinksMember(TeamRole::Admin);

    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'status' => TransactionStatus::Validated,
        'validated_by' => $admin->id,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
    ]);
    $packagingLine = SalePackagingLine::factory()->create([
        'sale_id' => $sale->id,
        'packaging_id' => $packaging->id,
        'quantity_out' => 5,
        'quantity_returned' => 0,
    ]);

    $this->actingAs($magasinier)
        ->post(route('drinks.sale-packaging-lines.deconsign', [
            'current_team' => $team->slug,
            'salePackagingLine' => $packagingLine->id,
        ]), ['quantity_returned' => 2])
        ->assertForbidden();
});
