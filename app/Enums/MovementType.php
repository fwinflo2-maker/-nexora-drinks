<?php

namespace App\Enums;

enum MovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';

    /**
     * Get the display label for the movement type.
     */
    public function label(): string
    {
        return match ($this) {
            self::In => 'Entrée',
            self::Out => 'Sortie',
            self::Transfer => 'Transfert',
            self::Adjustment => 'Ajustement',
        };
    }

    /**
     * Determine if this movement type increases stock.
     */
    public function increasesStock(): bool
    {
        return match ($this) {
            self::In, self::Adjustment => true,
            self::Out, self::Transfer => false,
        };
    }
}
