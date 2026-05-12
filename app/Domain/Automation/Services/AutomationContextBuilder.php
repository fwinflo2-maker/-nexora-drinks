<?php

namespace App\Domain\Automation\Services;

use App\Events\OrderConfirmed;
use App\Events\PaymentReceived;
use App\Events\StockMovementCreated;
use App\Models\Invoice;
use App\Models\StockLevel;

class AutomationContextBuilder
{
    /** @return array<string, mixed> */
    public function build(object $event): array
    {
        return match (true) {
            $event instanceof OrderConfirmed => $this->buildOrderContext($event),
            $event instanceof PaymentReceived => $this->buildPaymentContext($event),
            $event instanceof StockMovementCreated => $this->buildStockContext($event),
            default => [],
        };
    }

    /** @return array<string, mixed> */
    private function buildOrderContext(OrderConfirmed $event): array
    {
        $order = $event->order;

        $totalDebt = (float) Invoice::withoutGlobalScopes()
            ->where('team_id', $order->team_id)
            ->where('client_id', $order->client_id)
            ->whereIn('status', ['sent', 'overdue', 'partial'])
            ->selectRaw('SUM(total - paid_amount) as balance')
            ->value('balance');

        $client = $order->client;

        return [
            'order' => [
                'id' => $order->id,
                'total' => (float) $order->total,
                'client_id' => $order->client_id,
            ],
            'client' => [
                'id' => $client?->id,
                'total_debt' => $totalDebt,
                'credit_limit' => (float) ($client?->credit_limit ?? 0),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function buildPaymentContext(PaymentReceived $event): array
    {
        $payment = $event->payment;

        return [
            'payment' => [
                'id' => $payment->id,
                'amount' => (float) $payment->amount,
                'method' => $payment->method,
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function buildStockContext(StockMovementCreated $event): array
    {
        $movement = $event->movement;

        $stockLevel = StockLevel::withoutGlobalScopes()
            ->where('team_id', $movement->team_id)
            ->where('product_id', $movement->product_id)
            ->where('warehouse_id', $movement->warehouse_id)
            ->first();

        return [
            'stock' => [
                'quantity' => $stockLevel?->quantity ?? 0,
                'product_id' => $movement->product_id,
                'warehouse_id' => $movement->warehouse_id,
            ],
            'product' => [
                'id' => $movement->product_id,
                'min_threshold' => $stockLevel?->min_threshold ?? 0,
            ],
        ];
    }
}
