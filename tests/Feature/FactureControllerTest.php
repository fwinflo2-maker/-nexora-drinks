<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── index() ───────────────────────────────────────────────────────────────────

test('factures index retourne les factures paginées avec stats', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
        'status' => 'paid',
    ]);

    $this->actingAs($user)
        ->get(route('factures.index', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('factures/index')
            ->has('factures')
            ->has('stats')
            ->has('clients')
        );
});

test('factures index refuse un non-membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get(route('factures.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

// ── store() ───────────────────────────────────────────────────────────────────

test('store crée une facture pour la team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);

    $this->actingAs($user)
        ->post(route('factures.store', ['current_team' => $team->slug]), [
            'client_id' => $client->id,
            'due_date' => now()->addDays(30)->toDateString(),
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('invoices', [
        'team_id' => $team->id,
        'client_id' => $client->id,
    ]);
});

test('store valide les champs requis', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('factures.store', ['current_team' => $team->slug]), [])
        ->assertSessionHasErrors(['client_id']);
});

test('store génère un numéro de facture unique', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);

    $this->actingAs($user)
        ->post(route('factures.store', ['current_team' => $team->slug]), [
            'client_id' => $client->id,
        ])
        ->assertRedirect();

    $invoice = Invoice::where('team_id', $team->id)->first();
    expect($invoice->invoice_number)->toStartWith('FACT-'.date('Y').'-');
});

// ── show() ────────────────────────────────────────────────────────────────────

test('show retourne la facture avec ses relations', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $invoice = Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('factures.show', ['current_team' => $team->slug, 'invoice' => $invoice->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('factures/show')
            ->has('invoice')
        );
});

test('ne peut pas voir la facture d\'une autre team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam;
    $otherClient = Client::factory()->create(['team_id' => $otherTeam->id]);
    $otherInvoice = Invoice::factory()->create([
        'team_id' => $otherTeam->id,
        'client_id' => $otherClient->id,
        'created_by' => $otherUser->id,
    ]);

    // BelongsToTeam applique un global scope filtrant par current_team_id.
    // Le model binding retourne donc 404 car l'invoice d'une autre team est hors scope.
    $this->actingAs($user)
        ->get(route('factures.show', ['current_team' => $team->slug, 'invoice' => $otherInvoice->id]))
        ->assertNotFound();
});

// ── update() ──────────────────────────────────────────────────────────────────

test('update modifie le statut d\'une facture', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $invoice = Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    $this->actingAs($user)
        ->patch(route('factures.update', ['current_team' => $team->slug, 'invoice' => $invoice->id]), [
            'status' => 'sent',
        ])
        ->assertRedirect();

    expect($invoice->fresh()->status)->toBe('sent');
});

// ── destroy() ─────────────────────────────────────────────────────────────────

test('destroy supprime une facture en brouillon', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $invoice = Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
        'status' => 'draft',
    ]);

    $this->actingAs($user)
        ->delete(route('factures.destroy', ['current_team' => $team->slug, 'invoice' => $invoice->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
});

test('destroy refuse de supprimer une facture envoyée', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $invoice = Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
        'status' => 'sent',
    ]);

    $this->actingAs($user)
        ->delete(route('factures.destroy', ['current_team' => $team->slug, 'invoice' => $invoice->id]))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'deleted_at' => null]);
});

// ── storePaiement() ───────────────────────────────────────────────────────────

test('storePaiement enregistre un paiement sur une facture', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $invoice = Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
        'status' => 'sent',
        'total' => 50000,
        'paid_amount' => 0,
    ]);

    $this->actingAs($user)
        ->post(route('factures.paiements.store', ['current_team' => $team->slug, 'invoice' => $invoice->id]), [
            'amount' => 20000,
            'method' => 'cash',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('payments', [
        'team_id' => $team->id,
        'invoice_id' => $invoice->id,
        'amount' => 20000,
    ]);

    expect($invoice->fresh()->paid_amount)->toBe('20000.00');
});

test('facture passe en paid quand soldée', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $invoice = Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
        'status' => 'sent',
        'total' => 30000,
        'paid_amount' => 0,
    ]);

    $this->actingAs($user)
        ->post(route('factures.paiements.store', ['current_team' => $team->slug, 'invoice' => $invoice->id]), [
            'amount' => 30000,
            'method' => 'cash',
        ])
        ->assertRedirect();

    expect($invoice->fresh()->status)->toBe('paid');
});

test('storePaiement valide les champs requis', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $client = Client::factory()->create(['team_id' => $team->id]);
    $invoice = Invoice::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('factures.paiements.store', ['current_team' => $team->slug, 'invoice' => $invoice->id]), [])
        ->assertSessionHasErrors(['amount', 'method']);
});
