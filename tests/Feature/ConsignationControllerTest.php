<?php

use App\Models\Client;
use App\Models\ClientPackagingBalance;
use App\Models\PackagingType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── index() ───────────────────────────────────────────────────────────────────

test('consignations index retourne les clients avec leurs balances', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    $packagingType = PackagingType::factory()->create(['team_id' => $team->id]);
    ClientPackagingBalance::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'packaging_type_id' => $packagingType->id,
        'quantity_owed' => 3,
    ]);

    $this->actingAs($user)
        ->get(route('consignations.index', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('consignations/index')
            ->has('clients')
            ->has('packaging_types')
        );
});

test('consignations index refuse un non-membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get(route('consignations.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('consignations index isole les données par team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam;
    Client::factory()->create(['team_id' => $otherTeam->id, 'name' => 'Client autre team']);

    $this->actingAs($user)
        ->get(route('consignations.index', ['current_team' => $team->slug]))
        ->assertOk();

    expect(Client::where('team_id', $team->id)->count())->toBe(0);
});

// ── store() ───────────────────────────────────────────────────────────────────

test('store crée un type d\'emballage pour la team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('consignations.store', ['current_team' => $team->slug]), [
            'name' => 'Casier 24 bouteilles',
            'unit_value_xaf' => 500,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('packaging_types', [
        'team_id' => $team->id,
        'name' => 'Casier 24 bouteilles',
        'unit_value_xaf' => 500,
    ]);
});

test('store valide les champs requis', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('consignations.store', ['current_team' => $team->slug]), [])
        ->assertSessionHasErrors(['name', 'unit_value_xaf']);
});

// ── show() ────────────────────────────────────────────────────────────────────

test('show retourne l\'historique de consignation d\'un client', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);

    $this->actingAs($user)
        ->get(route('consignations.show', ['current_team' => $team->slug, 'client' => $client->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('consignations/show')
            ->has('client')
            ->has('historique')
        );
});

// ── storeMovement() ───────────────────────────────────────────────────────────

test('storeMovement enregistre un retour de consigne', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $packagingType = PackagingType::factory()->create(['team_id' => $team->id]);

    ClientPackagingBalance::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'packaging_type_id' => $packagingType->id,
        'quantity_owed' => 10,
    ]);

    $this->actingAs($user)
        ->post(route('consignations.mouvements.store', ['current_team' => $team->slug, 'client' => $client->id]), [
            'packaging_type_id' => $packagingType->id,
            'movement_type' => 'in',
            'quantity' => 3,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('packaging_movements', [
        'team_id' => $team->id,
        'client_id' => $client->id,
        'packaging_type_id' => $packagingType->id,
        'quantity' => 3,
        'movement_type' => 'in',
    ]);

    $balance = ClientPackagingBalance::where('client_id', $client->id)
        ->where('packaging_type_id', $packagingType->id)
        ->first();
    expect($balance->quantity_owed)->toBe(7);
});

test('storeMovement sortie augmente la dette du client', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $packagingType = PackagingType::factory()->create(['team_id' => $team->id]);

    ClientPackagingBalance::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'packaging_type_id' => $packagingType->id,
        'quantity_owed' => 5,
    ]);

    $this->actingAs($user)
        ->post(route('consignations.mouvements.store', ['current_team' => $team->slug, 'client' => $client->id]), [
            'packaging_type_id' => $packagingType->id,
            'movement_type' => 'out',
            'quantity' => 4,
        ])
        ->assertRedirect();

    $balance = ClientPackagingBalance::where('client_id', $client->id)->first();
    expect($balance->quantity_owed)->toBe(9);
});

test('storeMovement valide les champs requis', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);

    $this->actingAs($user)
        ->post(route('consignations.mouvements.store', ['current_team' => $team->slug, 'client' => $client->id]), [])
        ->assertSessionHasErrors(['packaging_type_id', 'movement_type', 'quantity']);
});
