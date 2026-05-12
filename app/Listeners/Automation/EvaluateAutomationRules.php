<?php

namespace App\Listeners\Automation;

use App\Domain\Automation\Models\AutomationRule;
use App\Events\DeliveryCompleted;
use App\Events\OrderConfirmed;
use App\Events\PaymentReceived;
use App\Events\StockMovementCreated;
use App\Jobs\RunAutomationRule;
use Illuminate\Contracts\Queue\ShouldQueue;

class EvaluateAutomationRules implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(object $event): void
    {
        $triggerName = $this->resolveTriggerName($event);
        $teamId = $this->resolveTeamId($event);

        if ($triggerName === null || $teamId === null) {
            return;
        }

        AutomationRule::withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->where('trigger_event', $triggerName)
            ->where('is_active', true)
            ->orderBy('priority')
            ->each(fn (AutomationRule $rule) => RunAutomationRule::dispatch($rule, $event));
    }

    private function resolveTriggerName(object $event): ?string
    {
        return match (true) {
            $event instanceof OrderConfirmed => 'order.confirming',
            $event instanceof DeliveryCompleted => 'delivery.completed',
            $event instanceof PaymentReceived => 'payment.received',
            $event instanceof StockMovementCreated => 'stock.movement.created',
            default => null,
        };
    }

    private function resolveTeamId(object $event): ?int
    {
        return match (true) {
            $event instanceof OrderConfirmed => $event->order->team_id,
            $event instanceof DeliveryCompleted => $event->delivery->team_id,
            $event instanceof PaymentReceived => $event->payment->team_id,
            $event instanceof StockMovementCreated => $event->movement->team_id,
            default => null,
        };
    }
}
