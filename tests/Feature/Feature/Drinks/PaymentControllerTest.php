<?php

use App\Enums\Drinks\PaymentMode;
use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Client;
use App\Models\Drinks\Payment;
use App\Models\Drinks\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Listing ───────────────────────────────────────────────────────────────────

test('caissier peut lister les règlements', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $this->actingAs($caissier)
        ->get(route('drinks.payments.index', ['current_team' => $team->slug]))
        ->assertOk();
});

// ── Create / Store ─────────────────────────────────────────────────────────────

test('caissier peut créer un règlement', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    $client = Client::factory()->create(['team_id' => $team->id]);

    $this->actingAs($caissier)
        ->post(route('drinks.payments.store', ['current_team' => $team->slug]), [
            'client_id' => $client->id,
            'amount' => 75000,
            'document_date' => today()->toDateString(),
            'mode' => PaymentMode::Cash->value,
        ])
        ->assertRedirect();

    $payment = Payment::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($payment)->not->toBeNull()
        ->and((float) $payment->amount)->toBe(75000.0)
        ->and($payment->code)->toStartWith('REG-');
});

// ── Allocate ──────────────────────────────────────────────────────────────────

test('un règlement peut être alloué à une vente validée', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    $client = Client::factory()->create(['team_id' => $team->id]);

    $sale = Sale::factory()->validated()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'total_ttc' => 100000,
    ]);

    $payment = Payment::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'amount' => 100000,
        'created_by' => $caissier->id,
    ]);

    $this->actingAs($caissier)
        ->post(route('drinks.payments.allocate', [
            'current_team' => $team->slug,
            'payment' => $payment->id,
        ]), ['sale_id' => $sale->id])
        ->assertRedirect();

    // After full allocation, payment should be validated
    expect($payment->fresh()->status)->toBe(TransactionStatus::Validated);
});

// ── Delete ────────────────────────────────────────────────────────────────────

test('caissier peut supprimer un règlement', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    $client = Client::factory()->create(['team_id' => $team->id]);

    $payment = Payment::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $caissier->id,
    ]);

    $this->actingAs($caissier)
        ->delete(route('drinks.payments.destroy', [
            'current_team' => $team->slug,
            'payment' => $payment->id,
        ]))
        ->assertRedirect();

    expect(Payment::withoutGlobalScopes()->find($payment->id))->toBeNull();
});

// ── 403 ───────────────────────────────────────────────────────────────────────

test('magasinier ne peut pas accéder aux règlements', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $this->actingAs($magasinier)
        ->get(route('drinks.payments.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('ops ne peut pas créer un règlement', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);
    $client = Client::factory()->create(['team_id' => $team->id]);

    $this->actingAs($ops)
        ->post(route('drinks.payments.store', ['current_team' => $team->slug]), [
            'client_id' => $client->id,
            'amount' => 50000,
            'document_date' => today()->toDateString(),
            'mode' => PaymentMode::Cash->value,
        ])
        ->assertForbidden();
});
