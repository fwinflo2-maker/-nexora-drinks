<?php

namespace App\Enums\Drinks;

enum TransactionStatus: string
{
    case Draft = 'draft';
    case Validated = 'validated';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Validated => 'Validé',
            self::Cancelled => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'amber',
            self::Validated => 'emerald',
            self::Cancelled => 'rose',
        };
    }
}
