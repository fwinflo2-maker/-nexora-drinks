<?php

namespace App\Enums;

enum WarehouseType: string
{
    case Main = 'main';
    case Secondary = 'secondary';
    case Truck = 'truck';

    /**
     * Get the display label for the warehouse type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Main => 'Dépôt principal',
            self::Secondary => 'Dépôt secondaire',
            self::Truck => 'Camion',
        };
    }
}
