<?php

use App\Domain\Journal\Services\JournalService;
use App\Enums\JournalEntryType;
use App\Models\Expense;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\OhadaChartOfAccountsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->team = $this->user->currentTeam;
    $this->actingAs($this->user);
    app(OhadaChartOfAccountsSeeder::class)->seedForTeam($this->team->id);
    $this->journal = app(JournalService::class);
});

test('GET entries returns paginated structure with data and meta', function () {
    $order = Order::factory()->for($this->team)->create(['total' => 20000]);

    $this->journal->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => 20000,
        'occurred_at' => now(),
        'source' => $order,
    ]);

    $this->getJson('/api/v1/business-journal/entries')
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

test('GET entries filters by type and returns only matching results', function () {
    $order = Order::factory()->for($this->team)->create(['total' => 30000]);
    $expense = Expense::factory()->for($this->team)->create(['amount' => 5000, 'date' => now()]);

    $this->journal->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => 30000,
        'occurred_at' => now(),
        'source' => $order,
    ]);

    $this->journal->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Expense,
        'amount' => 5000,
        'occurred_at' => now(),
        'source' => $expense,
    ]);

    $this->getJson('/api/v1/business-journal/entries?type=sale')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('GET summary returns by_type and totals with revenue, expenses and net', function () {
    $order = Order::factory()->for($this->team)->create(['total' => 100000]);

    $this->journal->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => 100000,
        'occurred_at' => now(),
        'source' => $order,
    ]);

    $this->getJson('/api/v1/business-journal/summary')
        ->assertOk()
        ->assertJsonStructure([
            'by_type',
            'totals' => ['revenue', 'expenses', 'net'],
        ]);
});
