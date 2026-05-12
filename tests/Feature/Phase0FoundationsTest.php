<?php

use App\Models\Client;
use App\Models\ClientPackagingBalance;
use App\Models\DeliveryRoute;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PackagingMovement;
use App\Models\PackagingType;
use App\Models\Team;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// ── Protection des routes (auth + team membership) ─────────────────────────

test('les guests sont redirigés vers login pour les routes stocks', function () {
    $team = Team::factory()->create();

    $this->get("/{$team->slug}/stocks")
        ->assertRedirect(route('login'));
});

test('les guests sont redirigés vers login pour les routes tournees', function () {
    $team = Team::factory()->create();

    $this->get("/{$team->slug}/tournees")
        ->assertRedirect(route('login'));
});

test('les guests sont redirigés vers login pour les routes factures', function () {
    $team = Team::factory()->create();

    $this->get("/{$team->slug}/factures")
        ->assertRedirect(route('login'));
});

test('les guests sont redirigés vers login pour les routes finances', function () {
    $team = Team::factory()->create();

    $this->get("/{$team->slug}/finances")
        ->assertRedirect(route('login'));
});

test('les guests sont redirigés vers login pour les routes equipe', function () {
    $team = Team::factory()->create();

    $this->get("/{$team->slug}/equipe")
        ->assertRedirect(route('login'));
});

test('les guests sont redirigés vers login pour les routes consignations', function () {
    $team = Team::factory()->create();

    $this->get("/{$team->slug}/consignations")
        ->assertRedirect(route('login'));
});

test('un utilisateur non-membre ne peut pas accéder aux routes de l\'équipe', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;

    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get("/{$team->slug}/stocks")
        ->assertForbidden();
});

test('un membre de l\'équipe peut accéder aux routes stocks', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('stocks.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('un membre de l\'équipe peut accéder aux routes tournees', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('tournees.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('un membre de l\'équipe peut accéder aux routes factures', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('factures.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('un membre de l\'équipe peut accéder aux routes finances', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('finances.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('un membre de l\'équipe peut accéder aux routes equipe', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('equipe.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('un membre de l\'équipe peut accéder aux routes consignations', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('consignations.index', ['current_team' => $team->slug]))
        ->assertOk();
});

// ── Bug ConsignmentController corrigé ─────────────────────────────────────

test('ConsignmentController::index fonctionne via route model binding', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('consignments.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('ConsignmentController::storeMovement fonctionne via route model binding', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $packagingType = PackagingType::factory()->create(['team_id' => $team->id]);
    $client = Client::factory()->create(['team_id' => $team->id]);

    $this->actingAs($user)
        ->post(route('consignments.movement', ['current_team' => $team->slug]), [
            'client_id' => $client->id,
            'packaging_type_id' => $packagingType->id,
            'movement_type' => 'out',
            'quantity' => 5,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('packaging_movements', [
        'team_id' => $team->id,
        'client_id' => $client->id,
        'packaging_type_id' => $packagingType->id,
        'quantity' => 5,
        'movement_type' => 'out',
    ]);
});

// ── Modèles — relations correctes ─────────────────────────────────────────

test('DeliveryRoute utilise la table routes', function () {
    expect((new DeliveryRoute)->getTable())->toBe('routes');
});

test('Order a les relations attendues', function () {
    $order = new Order;

    expect($order->client())->toBeInstanceOf(BelongsTo::class);
    expect($order->items())->toBeInstanceOf(HasMany::class);
    expect($order->invoice())->toBeInstanceOf(HasOne::class);
});

test('Invoice a les relations attendues', function () {
    $invoice = new Invoice;

    expect($invoice->client())->toBeInstanceOf(BelongsTo::class);
    expect($invoice->payments())->toBeInstanceOf(HasMany::class);
    expect($invoice->order())->toBeInstanceOf(BelongsTo::class);
});

test('PackagingType a les relations attendues', function () {
    $packagingType = new PackagingType;

    expect($packagingType->movements())->toBeInstanceOf(HasMany::class);
    expect($packagingType->clientBalances())->toBeInstanceOf(HasMany::class);
});

test('PackagingMovement a les relations attendues', function () {
    $movement = new PackagingMovement;

    expect($movement->client())->toBeInstanceOf(BelongsTo::class);
    expect($movement->packagingType())->toBeInstanceOf(BelongsTo::class);
    expect($movement->creator())->toBeInstanceOf(BelongsTo::class);
});

test('Vehicle a les relations attendues', function () {
    $vehicle = new Vehicle;

    expect($vehicle->driver())->toBeInstanceOf(BelongsTo::class);
    expect($vehicle->routes())->toBeInstanceOf(HasMany::class);
});

test('Expense a les casts attendus', function () {
    $expense = new Expense;
    $casts = $expense->getCasts();

    expect($casts)->toHaveKey('amount');
    expect($casts)->toHaveKey('date');
});

test('ClientPackagingBalance calcule totalValueXaf correctement', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $packagingType = PackagingType::factory()->create([
        'team_id' => $team->id,
        'unit_value_xaf' => 1000,
    ]);

    $balance = ClientPackagingBalance::factory()->create([
        'team_id' => $team->id,
        'packaging_type_id' => $packagingType->id,
        'quantity_owed' => 5,
    ]);

    $balance->load('packagingType');

    expect($balance->totalValueXaf())->toBe(5000.0);
});
