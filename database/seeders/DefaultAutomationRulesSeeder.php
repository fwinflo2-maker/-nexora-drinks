<?php

namespace Database\Seeders;

use App\Domain\Automation\Models\AutomationRule;
use Illuminate\Database\Seeder;

class DefaultAutomationRulesSeeder extends Seeder
{
    public function seedForTeam(int $teamId): void
    {
        $rules = [
            [
                'name' => 'Blocage commande - dépassement limite crédit',
                'trigger_event' => 'order.confirming',
                'condition_field' => 'client.total_debt',
                'condition_operator' => 'gt',
                'condition_value' => 'client.credit_limit',
                'action_type' => 'block_order',
                'priority' => 1,
            ],
            [
                'name' => 'Suggestion achat - stock sous seuil minimum',
                'trigger_event' => 'stock.movement.created',
                'condition_field' => 'stock.quantity',
                'condition_operator' => 'lt',
                'condition_value' => 'product.min_threshold',
                'action_type' => 'create_purchase_suggestion',
                'priority' => 10,
            ],
            [
                'name' => 'Rappel dette - facture en retard > 30 jours',
                'trigger_event' => 'invoice.overdue_checked',
                'condition_field' => 'invoice.overdue_days',
                'condition_operator' => 'gt',
                'condition_value' => '30',
                'action_type' => 'send_debt_reminder',
                'priority' => 20,
            ],
            [
                'name' => 'Alerte manager - visite score faible',
                'trigger_event' => 'visit.scored',
                'condition_field' => 'visit.score',
                'condition_operator' => 'lt',
                'condition_value' => '60',
                'action_type' => 'alert_manager',
                'priority' => 30,
            ],
        ];

        foreach ($rules as $rule) {
            AutomationRule::withoutGlobalScopes()->firstOrCreate(
                [
                    'team_id' => $teamId,
                    'trigger_event' => $rule['trigger_event'],
                    'action_type' => $rule['action_type'],
                ],
                array_merge($rule, ['team_id' => $teamId, 'is_active' => true, 'is_system' => true])
            );
        }
    }

    public function run(): void
    {
        $this->seedForTeam(1);
    }
}
