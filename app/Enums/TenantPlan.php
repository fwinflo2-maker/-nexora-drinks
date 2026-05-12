<?php

namespace App\Enums;

enum TenantPlan: string
{
    case Starter = 'starter';
    case Pro = 'pro';
    case Enterprise = 'enterprise';

    /**
     * Get the display label for the plan.
     */
    public function label(): string
    {
        return match ($this) {
            self::Starter => 'Starter',
            self::Pro => 'Pro',
            self::Enterprise => 'Enterprise',
        };
    }
}
