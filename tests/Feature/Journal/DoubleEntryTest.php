<?php

use App\Domain\Accounting\Models\LedgerLine;
use App\Domain\Accounting\Services\LedgerService;
use App\Domain\Journal\Models\BusinessJournalEntry;
use App\Domain\Journal\Services\JournalService;
use App\Enums\JournalEntryType;
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
    $this->service = app(JournalService::class);
});

test('each record produces 2 ledger lines with balanced debit/credit', function () {
    $order = Order::factory()->for($this->team)->create(['total' => 100000]);

    $entry = $this->service->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => 100000,
        'occurred_at' => now(),
        'source' => $order,
    ]);

    $lines = LedgerLine::withoutGlobalScopes()->where('journal_entry_id', $entry->id)->get();

    expect($lines)->toHaveCount(2)
        ->and((float) $lines->sum('debit'))->toEqual((float) $lines->sum('credit'));
});

test('LedgerService rejects unbalanced lines with RuntimeException', function () {
    $order = Order::factory()->for($this->team)->create(['total' => 50000]);

    $entry = BusinessJournalEntry::create([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale->value,
        'amount' => 50000,
        'occurred_at' => now(),
    ]);

    expect(fn () => app(LedgerService::class)->postLines($entry, [
        ['account_code' => '411', 'debit' => 50000.0, 'credit' => 0.0],
        ['account_code' => '701', 'debit' => 0.0, 'credit' => 30000.0],
    ]))->toThrow(RuntimeException::class);
});

test('trial balance is balanced after multiple transactions', function () {
    $order1 = Order::factory()->for($this->team)->create(['total' => 100000]);
    $order2 = Order::factory()->for($this->team)->create(['total' => 50000]);

    $this->service->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => 100000,
        'occurred_at' => now(),
        'source' => $order1,
    ]);

    $this->service->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => 50000,
        'occurred_at' => now(),
        'source' => $order2,
    ]);

    $rows = app(LedgerService::class)->trialBalance($this->team->id);
    $totalDebit = array_sum(array_column($rows, 'debit'));
    $totalCredit = array_sum(array_column($rows, 'credit'));

    expect(abs($totalDebit - $totalCredit))->toBeLessThan(0.001);
});
