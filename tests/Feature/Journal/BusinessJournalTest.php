<?php

use App\Domain\Journal\Models\BusinessJournalEntry;
use App\Domain\Journal\Services\JournalService;
use App\Enums\JournalEntryType;
use App\Events\OrderConfirmed;
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
});

test('OrderConfirmed event triggers journal entry creation via listener', function () {
    $order = Order::factory()->for($this->team)->create(['total' => 50000]);

    OrderConfirmed::dispatch($order);

    expect(
        BusinessJournalEntry::withoutGlobalScopes()
            ->where('team_id', $this->team->id)
            ->where('sourceable_type', Order::class)
            ->where('sourceable_id', $order->id)
            ->exists()
    )->toBeTrue();
});

test('JournalService records entry with correct amount and type', function () {
    $service = app(JournalService::class);
    $order = Order::factory()->for($this->team)->create(['total' => 75000]);

    $entry = $service->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => $order->total,
        'occurred_at' => now(),
        'source' => $order,
    ]);

    expect($entry)->toBeInstanceOf(BusinessJournalEntry::class)
        ->and($entry->entry_type)->toBe(JournalEntryType::Sale)
        ->and((float) $entry->amount)->toBe(75000.0)
        ->and($entry->sourceable_id)->toBe($order->id)
        ->and($entry->sourceable_type)->toBe(Order::class);
});

test('journal entries cannot be deleted', function () {
    $service = app(JournalService::class);
    $order = Order::factory()->for($this->team)->create(['total' => 10000]);

    $entry = $service->record([
        'team_id' => $this->team->id,
        'entry_type' => JournalEntryType::Sale,
        'amount' => 10000,
        'occurred_at' => now(),
        'source' => $order,
    ]);

    expect(fn () => $entry->delete())->toThrow(RuntimeException::class);
});
