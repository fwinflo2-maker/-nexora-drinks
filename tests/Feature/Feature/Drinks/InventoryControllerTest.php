<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\InventoryLine;
use App\Models\Drinks\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Listing ───────────────────────────────────────────────────────────────────

test('magasinier peut lister les inventaires', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    Inventory::factory()->count(3)->create(['team_id' => $team->id]);

    $this->actingAs($magasinier)
        ->get(route('drinks.inventories.index', ['current_team' => $team->slug]))
        ->assertOk();
});

// ── Create / Store ─────────────────────────────────────────────────────────────

test('magasinier peut créer un inventaire en brouillon', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);

    $this->actingAs($magasinier)
        ->post(route('drinks.inventories.store', ['current_team' => $team->slug]), [
            'document_date' => today()->toDateString(),
            'observation' => 'Inventaire test',
            'lines' => [
                ['article_id' => $article->id, 'counted_qty' => 45],
            ],
        ])
        ->assertRedirect();

    $inventory = Inventory::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($inventory)->not->toBeNull()
        ->and($inventory->status)->toBe(TransactionStatus::Draft)
        ->and(InventoryLine::where('inventory_id', $inventory->id)->count())->toBe(1);
});

// ── Validate → stock adjustment ────────────────────────────────────────────────

test('validation d\'un inventaire crée des mouvements de stock et ajuste les quantités', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);
    $inventory = Inventory::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Draft,
        'document_date' => today()->toDateString(),
        'created_by' => $magasinier->id,
    ]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 40, // delta = -10
    ]);

    $this->actingAs($magasinier)
        ->post(route('drinks.inventories.validate', [
            'current_team' => $team->slug,
            'inventory' => $inventory->id,
        ]))
        ->assertRedirect();

    $inventory->refresh();
    $article->refresh();

    expect($inventory->status)->toBe(TransactionStatus::Validated)
        ->and($inventory->validated_at)->not->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()
            ->where('source_type', 'Inventory')
            ->where('source_id', $inventory->id)
            ->count()
        )->toBe(1)
        ->and($article->stock_qty)->toBe(40);
});

test('pas de mouvement créé si counted_qty === stock_qty', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 30]);
    $inventory = Inventory::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Draft,
        'document_date' => today()->toDateString(),
        'created_by' => $magasinier->id,
    ]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 30, // delta = 0 → pas de mouvement
    ]);

    $this->actingAs($magasinier)
        ->post(route('drinks.inventories.validate', [
            'current_team' => $team->slug,
            'inventory' => $inventory->id,
        ]))
        ->assertRedirect();

    expect(StockMovement::withoutGlobalScopes()
        ->where('source_type', 'Inventory')
        ->where('source_id', $inventory->id)
        ->count()
    )->toBe(0);
});

// ── Cancel validation ──────────────────────────────────────────────────────────

test('annulation de validation remet l\'inventaire en brouillon et supprime les mouvements', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 40]);
    $inventory = Inventory::factory()->validated()->create([
        'team_id' => $team->id,
        'document_date' => today()->toDateString(),
        'created_by' => $magasinier->id,
        'validated_by' => $magasinier->id,
    ]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 40,
    ]);

    // Simulate a movement that was created during validation
    StockMovement::create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'kind' => StockMovementKind::InventoryAdjust,
        'quantity' => 10,
        'source_type' => 'Inventory',
        'source_id' => $inventory->id,
        'document_date' => today()->toDateString(),
        'created_by' => $magasinier->id,
    ]);

    $this->actingAs($magasinier)
        ->post(route('drinks.inventories.cancel-validation', [
            'current_team' => $team->slug,
            'inventory' => $inventory->id,
        ]))
        ->assertRedirect();

    $inventory->refresh();

    expect($inventory->status)->toBe(TransactionStatus::Draft)
        ->and($inventory->validated_at)->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()
            ->where('source_type', 'Inventory')
            ->where('source_id', $inventory->id)
            ->count()
        )->toBe(0);
});

// ── Autorisation (403) ─────────────────────────────────────────────────────────

test('caissier ne peut pas accéder aux inventaires', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $this->actingAs($caissier)
        ->get(route('drinks.inventories.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('caissier ne peut pas valider un inventaire', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    [$admin] = drinksMember(TeamRole::Admin);

    $inventory = Inventory::factory()->create([
        'team_id' => $team->id,
        'created_by' => $admin->id,
    ]);

    $this->actingAs($caissier)
        ->post(route('drinks.inventories.validate', [
            'current_team' => $team->slug,
            'inventory' => $inventory->id,
        ]))
        ->assertForbidden();
});
