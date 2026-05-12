<?php

use App\Models\Expense;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── index() ───────────────────────────────────────────────────────────────────

test('owner peut voir les finances', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('finances.index', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('finances/index')
            ->has('team')
            ->has('kpis')
            ->has('depenses')
            ->has('categories')
        );
});

test('non-membre ne peut pas voir les finances', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get(route('finances.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

// ── storeDepense() ────────────────────────────────────────────────────────────

test('owner peut enregistrer une dépense', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('finances.depenses.store', ['current_team' => $team->slug]), [
            'label' => 'Plein du camion',
            'category' => 'carburant',
            'amount' => 75000,
            'date' => '2026-04-30',
        ])
        ->assertRedirect();

    expect(Expense::where('team_id', $team->id)
        ->where('category', 'carburant')
        ->exists()
    )->toBeTrue();
});

test('storeDepense valide les champs requis', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('finances.depenses.store', ['current_team' => $team->slug]), [])
        ->assertSessionHasErrors(['label', 'category', 'amount', 'date']);
});

// ── destroyDepense() ──────────────────────────────────────────────────────────

test('owner peut supprimer sa dépense', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $expense = Expense::factory()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('finances.depenses.destroy', ['current_team' => $team->slug, 'expense' => $expense->id]))
        ->assertRedirect();

    expect(Expense::find($expense->id))->toBeNull();
});

test('ne peut pas supprimer une dépense d\'une autre team', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;

    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam;

    $expense = Expense::factory()->create([
        'team_id' => $otherTeam->id,
        'created_by' => $otherUser->id,
    ]);

    // Le global scope BelongsToTeam filtre les expenses par current_team_id de l'user authentifié.
    // L'expense d'une autre team est invisible → 404 (plus sécurisé que 403 qui révèle l'existence).
    $this->actingAs($owner)
        ->delete(route('finances.depenses.destroy', ['current_team' => $team->slug, 'expense' => $expense->id]))
        ->assertNotFound();
});

// ── rapports() ────────────────────────────────────────────────────────────────

test('owner peut voir les rapports', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('finances.rapports', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('finances/rapports')
            ->has('team')
            ->has('monthly')
            ->has('by_category')
            ->has('year')
        );
});
