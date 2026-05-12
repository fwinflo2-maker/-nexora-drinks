<?php

namespace App\Enums;

enum AutomationOperator: string
{
    case GreaterThan = 'gt';
    case LessThan = 'lt';
    case GreaterThanOrEqual = 'gte';
    case LessThanOrEqual = 'lte';
    case Equals = 'eq';
    case NotEquals = 'neq';

    public function label(): string
    {
        return match ($this) {
            self::GreaterThan => 'Supérieur à',
            self::LessThan => 'Inférieur à',
            self::GreaterThanOrEqual => 'Supérieur ou égal à',
            self::LessThanOrEqual => 'Inférieur ou égal à',
            self::Equals => 'Égal à',
            self::NotEquals => 'Différent de',
        };
    }

    public function evaluate(mixed $actual, mixed $threshold): bool
    {
        return match ($this) {
            self::GreaterThan => $actual > $threshold,
            self::LessThan => $actual < $threshold,
            self::GreaterThanOrEqual => $actual >= $threshold,
            self::LessThanOrEqual => $actual <= $threshold,
            self::Equals => $actual == $threshold,
            self::NotEquals => $actual != $threshold,
        };
    }
}
