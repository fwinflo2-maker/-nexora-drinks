<?php

namespace App\Domain\Automation\Models;

use App\Concerns\BelongsToTeam;
use App\Enums\AutomationAction;
use App\Enums\AutomationOperator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'team_id', 'name', 'description', 'trigger_event',
    'condition_field', 'condition_operator', 'condition_value',
    'action_type', 'action_params', 'is_active', 'priority', 'is_system',
])]
class AutomationRule extends Model
{
    use BelongsToTeam;

    /**
     * Evaluate whether this rule's condition is met given a context array.
     *
     * @param  array<string, mixed>  $context
     */
    public function evaluate(array $context): bool
    {
        $actual = data_get($context, $this->condition_field);

        if ($actual === null) {
            return false;
        }

        return $this->condition_operator->evaluate($actual, $this->condition_value);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition_operator' => AutomationOperator::class,
            'action_type' => AutomationAction::class,
            'action_params' => 'array',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }
}
