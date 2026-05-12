<?php

namespace App\Domain\Automation\Services;

use App\Domain\Automation\Models\AutomationRule;
use App\Enums\AutomationAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutomationActionExecutor
{
    /** @param array<string, mixed> $context */
    public function execute(AutomationRule $rule, array $context): void
    {
        match ($rule->action_type) {
            AutomationAction::BlockOrder => $this->blockOrder($rule, $context),
            AutomationAction::CreatePurchaseSuggestion => $this->createPurchaseSuggestion($rule, $context),
            AutomationAction::SendDebtReminder => $this->sendDebtReminder($rule, $context),
            AutomationAction::AlertManager => $this->alertManager($rule, $context),
            AutomationAction::SendNotification => $this->sendNotification($rule, $context),
        };
    }

    /** @param array<string, mixed> $context */
    private function blockOrder(AutomationRule $rule, array $context): void
    {
        $orderId = data_get($context, 'order.id');
        $teamId = $rule->team_id;

        Cache::put("team:{$teamId}:order:{$orderId}:blocked", true, now()->addMinutes(5));

        Log::warning('AutomationEngine: commande bloquée', [
            'rule' => $rule->name,
            'order_id' => $orderId,
            'team_id' => $teamId,
        ]);
    }

    /** @param array<string, mixed> $context */
    private function createPurchaseSuggestion(AutomationRule $rule, array $context): void
    {
        Log::info('AutomationEngine: suggestion achat créée (placeholder GAP-02)', [
            'rule' => $rule->name,
            'product_id' => data_get($context, 'stock.product_id'),
            'team_id' => $rule->team_id,
        ]);
    }

    /** @param array<string, mixed> $context */
    private function sendDebtReminder(AutomationRule $rule, array $context): void
    {
        Log::info('AutomationEngine: rappel dette envoyé (placeholder notifications)', [
            'rule' => $rule->name,
            'team_id' => $rule->team_id,
            'context' => $context,
        ]);
    }

    /** @param array<string, mixed> $context */
    private function alertManager(AutomationRule $rule, array $context): void
    {
        Log::warning('AutomationEngine: alerte manager', [
            'rule' => $rule->name,
            'team_id' => $rule->team_id,
            'context' => $context,
        ]);
    }

    /** @param array<string, mixed> $context */
    private function sendNotification(AutomationRule $rule, array $context): void
    {
        Log::info('AutomationEngine: notification envoyée (placeholder)', [
            'rule' => $rule->name,
            'team_id' => $rule->team_id,
        ]);
    }
}
