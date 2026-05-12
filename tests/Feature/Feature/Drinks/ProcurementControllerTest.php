<?php

use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\ProcurementArticleLine;
use App\Models\Drinks\StockMovement;
use App\Models\Drinks\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin peut lister les approvisionnements', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    Procurement::factory()->count(3)->create(['team_id' => $team->id]);

    $this->actingAs($admin)
        ->get(route('drinks.procurements.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('admin peut créer un approvisionnement en brouillon', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $supplier = Supplier::factory()->create(['team_id' => $team->id]);
    $article = Article::factory()->create(['team_id' => $team->id]);

    $this->actingAs($admin)
        ->post(route('drinks.procurements.store', ['current_team' => $team->slug]), [
            'kind' => 'normal',
            'document_date' => today()->toDateString(),
            'supplier_id' => $supplier->id,
            'observation' => 'Test',
            'lines' => [
                ['article_id' => $article->id, 'quantity' => 10, 'unit_price' => 500],
            ],
        ])
        ->assertRedirect();

    $procurement = Procurement::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($procurement)->not->toBeNull()
        ->and($procurement->status)->toBe(TransactionStatus::Draft)
        ->and((float) $procurement->total_ht)->toEqual(5000.0)
        ->and(ProcurementArticleLine::where('procurement_id', $procurement->id)->count())->toBe(1);
});

test('validation d\'un approvisionnement crée des mouvements de stock', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);
    $procurement = Procurement::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Draft,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
    ]);
    ProcurementArticleLine::factory()->create([
        'procurement_id' => $procurement->id,
        'article_id' => $article->id,
        'quantity_received' => 20,
        'unit_price' => 300,
        'amount' => 6000,
    ]);

    $this->actingAs($admin)
        ->post(route('drinks.procurements.validate', [
            'current_team' => $team->slug,
            'procurement' => $procurement->id,
        ]))
        ->assertRedirect();

    $procurement->refresh();

    expect($procurement->status)->toBe(TransactionStatus::Validated)
        ->and($procurement->validated_at)->not->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()->where('source_id', $procurement->id)->count())->toBe(1);
});

test('annulation de validation remet le statut en brouillon', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 20]);
    $procurement = Procurement::factory()->validated()->create([
        'team_id' => $team->id,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
        'validated_by' => $admin->id,
    ]);
    ProcurementArticleLine::factory()->create([
        'procurement_id' => $procurement->id,
        'article_id' => $article->id,
        'quantity_received' => 20,
        'unit_price' => 300,
        'amount' => 6000,
    ]);

    StockMovement::create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'kind' => 'procurement_in',
        'quantity' => 20,
        'source_type' => 'Procurement',
        'source_id' => $procurement->id,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('drinks.procurements.cancel-validation', [
            'current_team' => $team->slug,
            'procurement' => $procurement->id,
        ]))
        ->assertRedirect();

    $procurement->refresh();

    expect($procurement->status)->toBe(TransactionStatus::Draft)
        ->and($procurement->validated_at)->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()->where('source_id', $procurement->id)->count())->toBe(0);
});

test('caissier ne peut pas accéder aux approvisionnements', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $this->actingAs($caissier)
        ->get(route('drinks.procurements.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('caissier ne peut pas valider un approvisionnement', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    [$admin] = drinksMember(TeamRole::Admin);

    $procurement = Procurement::factory()->create([
        'team_id' => $team->id,
        'created_by' => $admin->id,
    ]);

    $this->actingAs($caissier)
        ->post(route('drinks.procurements.validate', [
            'current_team' => $team->slug,
            'procurement' => $procurement->id,
        ]))
        ->assertForbidden();
});
