<?php

declare(strict_types=1);

namespace App\Enums\Hotel;

enum RoomStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Maintenance = 'maintenance';
    case Reserved = 'reserved';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Occupied => 'Occupée',
            self::Maintenance => 'Maintenance',
            self::Reserved => 'Réservée',
        };
    }
}
