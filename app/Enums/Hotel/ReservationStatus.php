<?php

declare(strict_types=1);

namespace App\Enums\Hotel;

enum ReservationStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Confirmed => 'Confirmée',
            self::CheckedIn => 'En cours',
            self::CheckedOut => 'Départ effectué',
            self::Cancelled => 'Annulée',
        };
    }
}
