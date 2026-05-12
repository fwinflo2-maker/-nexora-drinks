<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Drinks\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Listing ───────────────────────────────────────────────────────────────────

test('magasinier peut consulter le journal des mouvements', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id]);
    StockMovement::create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'kind' => StockMovementKind::ProcurementIn,
        'quantity' => 10,
        'source_type' => 'Procurement',
        'source_id' => 1,
        'document_date' => today()->toDateString(),
        'created_by' => $magasinier->id,
    ]);

    $this->actingAs($magasinier)
        ->get(route('drinks.stock-movements.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('ops peut consulter le journal des mouvements', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);

    $this->actingAs($ops)
        ->get(route('drinks.stock-movements.index', ['current_team' => $team->slug]))
        ->assertOk();
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('magasinier peut voir le détail d\'un mouvement', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id]);
    $movement = StockMovement::create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'kind' => StockMovementKind::InventoryAdjust,
        'quantity' => 5,
        'source_type' => 'Inventory',
        'source_id' => 1,
        'document_date' => today()->toDateString(),
        'created_by' => $magasinier->id,
    ]);

    $this->actingAs($magasinier)
        ->get(route('drinks.stock-movements.show', [
            'current_team' => $team->slug,
            'stock_movement' => $movement->id,
        ]))
        ->assertOk();
});

// ── Autorisation (403) ─────────────────────────────────────────────────────────

test('caissier ne peut pas voir le journal des mouvements', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $this->actingAs($caissier)
        ->get(route('drinks.stock-movements.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('comptable ne peut pas voir le journal des mouvements', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $this->actingAs($comptable)
        ->get(route('drinks.stock-movements.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});
