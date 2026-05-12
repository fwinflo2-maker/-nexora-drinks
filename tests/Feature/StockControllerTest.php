<?php

use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── index() ───────────────────────────────────────────────────────────────────

test('stock index retourne les produits, entrepôts et stats de la team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $warehouse = Warehouse::factory()->create(['team_id' => $team->id]);
    $product = Product::factory()->create(['team_id' => $team->id]);
    StockLevel::factory()->create([
        'team_id' => $team->id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 5,
        'min_threshold' => 10,
    ]);

    $this->actingAs($user)
        ->get(route('stocks.index', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('stocks/index')
            ->has('products')
            ->has('warehouses')
            ->has('stats')
        );
});

test('stock index isole les données par team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam;
    Product::factory()->create(['team_id' => $otherTeam->id, 'name' => 'Produit autre team']);

    $this->actingAs($user)
        ->get(route('stocks.index', ['current_team' => $team->slug]))
        ->assertOk();

    expect(Product::where('team_id', $team->id)->count())->toBe(0);
});

test('un non-membre ne peut pas accéder à stock index', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get(route('stocks.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

// ── mouvements() ──────────────────────────────────────────────────────────────

test('stock mouvements retourne les mouvements paginés avec relations', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $warehouse = Warehouse::factory()->create(['team_id' => $team->id]);
    $product = Product::factory()->create(['team_id' => $team->id]);
    StockMovement::factory()->create([
        'team_id' => $team->id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $this->actingAs($user)
        ->get(route('stocks.mouvements', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('stocks/mouvements')
            ->has('mouvements')
        );
});

// ── storeMovement() ───────────────────────────────────────────────────────────

test('storeMovement crée un mouvement et met à jour le stock level', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $warehouse = Warehouse::factory()->create(['team_id' => $team->id]);
    $product = Product::factory()->create(['team_id' => $team->id]);
    StockLevel::factory()->create([
        'team_id' => $team->id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 10,
    ]);

    $this->actingAs($user)
        ->post(route('stocks.mouvements.store', ['current_team' => $team->slug]), [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => 'in',
            'quantity' => 5,
            'notes' => 'Test entrée',
        ])
        ->assertRedirect();

    expect(StockMovement::where('team_id', $team->id)->count())->toBe(1);

    $level = StockLevel::where('product_id', $product->id)
        ->where('warehouse_id', $warehouse->id)
        ->first();
    expect($level->quantity)->toBe(15);
});

test('storeMovement out diminue le stock level', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $warehouse = Warehouse::factory()->create(['team_id' => $team->id]);
    $product = Product::factory()->create(['team_id' => $team->id]);
    StockLevel::factory()->create([
        'team_id' => $team->id,
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 20,
    ]);

    $this->actingAs($user)
        ->post(route('stocks.mouvements.store', ['current_team' => $team->slug]), [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => 'out',
            'quantity' => 8,
        ])
        ->assertRedirect();

    $level = StockLevel::where('product_id', $product->id)->first();
    expect($level->quantity)->toBe(12);
});

test('storeMovement valide les champs requis', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('stocks.mouvements.store', ['current_team' => $team->slug]), [])
        ->assertSessionHasErrors(['product_id', 'warehouse_id', 'movement_type', 'quantity']);
});

test('storeMovement refuse un non-membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->post(route('stocks.mouvements.store', ['current_team' => $team->slug]), [
            'product_id' => 1,
            'warehouse_id' => 1,
            'movement_type' => 'in',
            'quantity' => 5,
        ])
        ->assertForbidden();
});

// ── rangement() ───────────────────────────────────────────────────────────────

test('rangement retourne les entrepôts avec leurs niveaux de stock', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    Warehouse::factory()->create(['team_id' => $team->id]);

    $this->actingAs($user)
        ->get(route('stocks.rangement', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('stocks/rangement')
            ->has('warehouses')
        );
});

// ── picking() ─────────────────────────────────────────────────────────────────

test('picking retourne les données pour les tournées à venir', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('stocks.picking', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('stocks/picking')
            ->has('picking_lists')
        );
});
