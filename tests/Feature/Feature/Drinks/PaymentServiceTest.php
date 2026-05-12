<?php

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Client;
use App\Models\Drinks\Payment;
use App\Models\Drinks\PaymentAdjustment;
use App\Models\Drinks\Sale;
use App\Models\User;
use App\Services\Drinks\PaymentService;

test('allocate crée un PaymentAdjustment liant le paiement à la vente', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 50000]);
    $payment = Payment::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'amount' => 50000, 'created_by' => $user->id]);

    $adjustment = app(PaymentService::class)->allocate($payment, $sale);

    expect($adjustment)->toBeInstanceOf(PaymentAdjustment::class)
        ->and($adjustment->sale_id)->toBe($sale->id)
        ->and($adjustment->team_id)->toBe($team->id)
        ->and((float) $adjustment->amount)->toBe(50000.0);
});

test('allocate met à jour le payment status à Validated quand la vente est entièrement réglée', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 30000]);
    $payment = Payment::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'amount' => 30000, 'created_by' => $user->id]);

    app(PaymentService::class)->allocate($payment, $sale);

    expect($payment->fresh()->status)->toBe(TransactionStatus::Validated);
});

test('allocate ne valide pas le payment si le montant est insuffisant', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 100000]);
    $payment = Payment::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'amount' => 40000, 'created_by' => $user->id, 'status' => TransactionStatus::Draft]);

    app(PaymentService::class)->allocate($payment, $sale);

    expect($payment->fresh()->status)->toBe(TransactionStatus::Draft);
});

test('allocate utilise updateOrCreate pour éviter les doublons', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 50000]);
    $payment = Payment::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'amount' => 20000, 'created_by' => $user->id]);

    $service = app(PaymentService::class);
    $service->allocate($payment, $sale);
    $service->allocate($payment, $sale);

    // Un seul adjustment pour cette paire team/sale
    expect(PaymentAdjustment::where('sale_id', $sale->id)->where('team_id', $team->id)->count())->toBe(1);
});

test('allocate passe une observation si fournie', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 50000]);
    $payment = Payment::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'amount' => 50000, 'created_by' => $user->id]);

    $adjustment = app(PaymentService::class)->allocate($payment, $sale, 'Règlement partiel janvier');

    expect($adjustment->observation)->toBe('Règlement partiel janvier');
});
