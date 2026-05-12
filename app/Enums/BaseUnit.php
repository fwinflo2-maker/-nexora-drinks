<?php

namespace App\Enums;

enum BaseUnit: string
{
    case Bouteille = 'bouteille';
    case Pack = 'pack';
    case Casier = 'casier';
    case Palette = 'palette';

    /**
     * Get the display label for the unit.
     */
    public function label(): string
    {
        return match ($this) {
            self::Bouteille => 'Bouteille',
            self::Pack => 'Pack / Lot',
            self::Casier => 'Casier / Carton',
            self::Palette => 'Palette',
        };
    }
}
