<?php

use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\CashDeposit;
use App\Models\Drinks\CashInput;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── CashInput ─────────────────────────────────────────────────────────────────

test('comptable peut lister les apports caisse', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $this->actingAs($comptable)
        ->get(route('drinks.cash-inputs.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('comptable peut créer un apport caisse en brouillon', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $this->actingAs($comptable)
        ->post(route('drinks.cash-inputs.store', ['current_team' => $team->slug]), [
            'amount' => 150000,
            'document_date' => today()->toDateString(),
        ])
        ->assertRedirect();

    $input = CashInput::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($input)->not->toBeNull()
        ->and((float) $input->amount)->toBe(150000.0)
        ->and($input->code)->toStartWith('APR-');
});

test('validation d\'un apport caisse passe à Validated', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $input = CashInput::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Draft,
        'created_by' => $comptable->id,
    ]);

    $this->actingAs($comptable)
        ->post(route('drinks.cash-inputs.validate', [
            'current_team' => $team->slug,
            'cashInput' => $input->id,
        ]))
        ->assertRedirect();

    expect($input->fresh()->status)->toBe(TransactionStatus::Validated);
});

test('annulation de validation d\'un apport repasse à Draft', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $input = CashInput::factory()->validated()->create([
        'team_id' => $team->id,
        'validated_by' => $comptable->id,
        'created_by' => $comptable->id,
    ]);

    $this->actingAs($comptable)
        ->post(route('drinks.cash-inputs.cancel-validation', [
            'current_team' => $team->slug,
            'cashInput' => $input->id,
        ]))
        ->assertRedirect();

    expect($input->fresh()->status)->toBe(TransactionStatus::Draft)
        ->and($input->fresh()->validated_at)->toBeNull();
});

// ── CashDeposit ───────────────────────────────────────────────────────────────

test('comptable peut créer un versement caisse', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $this->actingAs($comptable)
        ->post(route('drinks.cash-deposits.store', ['current_team' => $team->slug]), [
            'amount_cash' => 100000,
            'amount_cheque' => 50000,
            'amount_other' => 0,
            'document_date' => today()->toDateString(),
        ])
        ->assertRedirect();

    $deposit = CashDeposit::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($deposit)->not->toBeNull()
        ->and((float) $deposit->total_amount)->toBe(150000.0)
        ->and($deposit->code)->toStartWith('VRS-');
});

test('validation d\'un versement caisse passe à Validated', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $deposit = CashDeposit::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Draft,
        'created_by' => $comptable->id,
    ]);

    $this->actingAs($comptable)
        ->post(route('drinks.cash-deposits.validate', [
            'current_team' => $team->slug,
            'cashDeposit' => $deposit->id,
        ]))
        ->assertRedirect();

    expect($deposit->fresh()->status)->toBe(TransactionStatus::Validated);
});

// ── 403 ───────────────────────────────────────────────────────────────────────

test('magasinier ne peut pas accéder aux apports caisse', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $this->actingAs($magasinier)
        ->get(route('drinks.cash-inputs.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('ops ne peut pas accéder aux versements caisse', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);

    $this->actingAs($ops)
        ->get(route('drinks.cash-deposits.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});
