<?php

declare(strict_types=1);

namespace App\Enums\Hotel;

enum FolioType: string
{
    case Room = 'room';
    case Service = 'service';
    case Extra = 'extra';
    case Discount = 'discount';
    case Restaurant = 'restaurant';
    case Payment = 'payment';

    public function label(): string
    {
        return match ($this) {
            self::Room => 'Hébergement',
            self::Service => 'Service',
            self::Extra => 'Extra',
            self::Discount => 'Remise',
            self::Restaurant => 'Restaurant',
            self::Payment => 'Paiement',
        };
    }
}
