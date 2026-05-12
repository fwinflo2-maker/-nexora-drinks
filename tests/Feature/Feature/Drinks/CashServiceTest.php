<?php

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\CashDeposit;
use App\Models\Drinks\CashInput;
use App\Models\Drinks\Expense;
use App\Models\Drinks\Payment;
use App\Models\User;
use App\Services\Drinks\CashService;

test('totalCashInputs retourne le montant et le count des cash inputs validés de la période', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    CashInput::factory()->create(['team_id' => $team->id, 'amount' => 50000, 'document_date' => '2026-05-01', 'status' => TransactionStatus::Validated]);
    CashInput::factory()->create(['team_id' => $team->id, 'amount' => 30000, 'document_date' => '2026-05-03', 'status' => TransactionStatus::Validated]);
    CashInput::factory()->create(['team_id' => $team->id, 'amount' => 10000, 'document_date' => '2026-05-01', 'status' => TransactionStatus::Draft]); // non validé
    CashInput::factory()->create(['team_id' => $team->id, 'amount' => 20000, 'document_date' => '2026-06-01', 'status' => TransactionStatus::Validated]); // hors période

    $result = app(CashService::class)->totalCashInputs($team->id, '2026-05-01', '2026-05-31');

    expect($result['amount'])->toBe(80000.0)
        ->and($result['count'])->toBe(2);
});

test('totalCashInputs isole par team_id', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam;

    CashInput::factory()->create(['team_id' => $team->id, 'amount' => 10000, 'document_date' => '2026-05-01', 'status' => TransactionStatus::Validated]);
    CashInput::factory()->create(['team_id' => $otherTeam->id, 'amount' => 99999, 'document_date' => '2026-05-01', 'status' => TransactionStatus::Validated]);

    $result = app(CashService::class)->totalCashInputs($team->id, '2026-05-01', '2026-05-31');

    expect($result['amount'])->toBe(10000.0)
        ->and($result['count'])->toBe(1);
});

test('totalCashInputs retourne zéro si aucun résultat', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $result = app(CashService::class)->totalCashInputs($team->id, '2026-05-01', '2026-05-31');

    expect($result['amount'])->toBe(0.0)
        ->and($result['count'])->toBe(0);
});

test('totalCashDeposits agrège les versements validés', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    CashDeposit::factory()->create(['team_id' => $team->id, 'total_amount' => 200000, 'document_date' => '2026-05-05', 'status' => TransactionStatus::Validated]);
    CashDeposit::factory()->create(['team_id' => $team->id, 'total_amount' => 150000, 'document_date' => '2026-05-10', 'status' => TransactionStatus::Validated]);
    CashDeposit::factory()->create(['team_id' => $team->id, 'total_amount' => 50000, 'document_date' => '2026-05-10', 'status' => TransactionStatus::Draft]); // non validé

    $result = app(CashService::class)->totalCashDeposits($team->id, '2026-05-01', '2026-05-31');

    expect($result['amount'])->toBe(350000.0)
        ->and($result['count'])->toBe(2);
});

test('totalExpenses agrège les dépenses validées', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    Expense::factory()->create(['team_id' => $team->id, 'amount' => 15000, 'document_date' => '2026-05-02', 'status' => TransactionStatus::Validated]);
    Expense::factory()->create(['team_id' => $team->id, 'amount' => 8000, 'document_date' => '2026-05-15', 'status' => TransactionStatus::Validated]);

    $result = app(CashService::class)->totalExpenses($team->id, '2026-05-01', '2026-05-31');

    expect($result['amount'])->toBe(23000.0)
        ->and($result['count'])->toBe(2);
});

test('totalPayments agrège les paiements de la période', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    Payment::factory()->create(['team_id' => $team->id, 'amount' => 75000, 'document_date' => '2026-05-04']);
    Payment::factory()->create(['team_id' => $team->id, 'amount' => 25000, 'document_date' => '2026-05-18']);
    Payment::factory()->create(['team_id' => $team->id, 'amount' => 10000, 'document_date' => '2026-06-01']); // hors période

    $result = app(CashService::class)->totalPayments($team->id, '2026-05-01', '2026-05-31');

    expect($result['amount'])->toBe(100000.0)
        ->and($result['count'])->toBe(2);
});
