<?php

namespace App\Listeners\Journal;

use App\Domain\Journal\Services\JournalService;
use App\Enums\JournalEntryType;
use App\Events\OrderConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordOrderSaleEntry implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(private readonly JournalService $journalService) {}

    public function handle(OrderConfirmed $event): void
    {
        $order = $event->order;

        $this->journalService->record([
            'team_id' => $order->team_id,
            'entry_type' => JournalEntryType::Sale,
            'amount' => $order->total,
            'occurred_at' => $order->created_at,
            'reference_number' => $order->order_number,
            'description' => "Vente commande #{$order->order_number}",
            'source' => $order,
            'metadata' => [
                'client_id' => $order->client_id,
                'channel' => $order->channel,
                'subtotal' => $order->subtotal,
                'discount' => $order->discount_amount,
            ],
        ]);
    }
}
