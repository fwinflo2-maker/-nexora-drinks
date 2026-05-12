<?php

namespace App\Listeners\Journal;

use App\Domain\Journal\Services\JournalService;
use App\Enums\JournalEntryType;
use App\Enums\MovementType;
use App\Events\StockMovementCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordStockMovementEntry implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(private readonly JournalService $journalService) {}

    public function handle(StockMovementCreated $event): void
    {
        $movement = $event->movement;
        $amount = (float) $movement->quantity * (float) $movement->unit_cost;

        if ($amount <= 0) {
            return;
        }

        $entryType = match ($movement->movement_type) {
            MovementType::In => JournalEntryType::StockIn,
            MovementType::Out => JournalEntryType::StockOut,
            default => JournalEntryType::Adjustment,
        };

        $this->journalService->record([
            'team_id' => $movement->team_id,
            'entry_type' => $entryType,
            'amount' => $amount,
            'occurred_at' => $movement->created_at,
            'description' => "Mouvement stock #{$movement->id} ({$movement->movement_type->label()})",
            'source' => $movement,
            'metadata' => [
                'product_id' => $movement->product_id,
                'warehouse_id' => $movement->warehouse_id,
                'quantity' => $movement->quantity,
                'unit_cost' => $movement->unit_cost,
            ],
        ]);
    }
}
