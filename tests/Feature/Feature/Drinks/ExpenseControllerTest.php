<?php

use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Expense;
use App\Models\Drinks\ExpenseType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Listing ───────────────────────────────────────────────────────────────────

test('comptable peut lister les charges', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);
    Expense::factory()->count(3)->create(['team_id' => $team->id]);

    $this->actingAs($comptable)
        ->get(route('drinks.expenses.index', ['current_team' => $team->slug]))
        ->assertOk();
});

// ── Create / Store ─────────────────────────────────────────────────────────────

test('comptable peut créer une charge en brouillon', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);
    $type = ExpenseType::factory()->create(['team_id' => $team->id]);

    $this->actingAs($comptable)
        ->post(route('drinks.expenses.store', ['current_team' => $team->slug]), [
            'expense_type_id' => $type->id,
            'amount' => 50000,
            'document_date' => today()->toDateString(),
        ])
        ->assertRedirect();

    $expense = Expense::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($expense)->not->toBeNull()
        ->and($expense->status)->toBe(TransactionStatus::Draft)
        ->and($expense->amount)->toBe('50000.00');
});

// ── Validate ──────────────────────────────────────────────────────────────────

test('validation d\'une charge passe son statut à Validated', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $expense = Expense::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Draft,
        'created_by' => $comptable->id,
    ]);

    $this->actingAs($comptable)
        ->post(route('drinks.expenses.validate', [
            'current_team' => $team->slug,
            'expense' => $expense->id,
        ]))
        ->assertRedirect();

    expect($expense->fresh()->status)->toBe(TransactionStatus::Validated)
        ->and($expense->fresh()->validated_at)->not->toBeNull();
});

// ── Cancel validation ──────────────────────────────────────────────────────────

test('annulation de validation remet la charge en brouillon', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $expense = Expense::factory()->validated()->create([
        'team_id' => $team->id,
        'validated_by' => $comptable->id,
        'created_by' => $comptable->id,
    ]);

    $this->actingAs($comptable)
        ->post(route('drinks.expenses.cancel-validation', [
            'current_team' => $team->slug,
            'expense' => $expense->id,
        ]))
        ->assertRedirect();

    expect($expense->fresh()->status)->toBe(TransactionStatus::Draft)
        ->and($expense->fresh()->validated_at)->toBeNull();
});

// ── Autorisation (403) ─────────────────────────────────────────────────────────

test('magasinier ne peut pas accéder aux charges', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $this->actingAs($magasinier)
        ->get(route('drinks.expenses.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('caissier ne peut pas valider une charge', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    [$comptable] = drinksMember(TeamRole::Comptable);

    $expense = Expense::factory()->create([
        'team_id' => $team->id,
        'created_by' => $comptable->id,
    ]);

    $this->actingAs($caissier)
        ->post(route('drinks.expenses.validate', [
            'current_team' => $team->slug,
            'expense' => $expense->id,
        ]))
        ->assertForbidden();
});
