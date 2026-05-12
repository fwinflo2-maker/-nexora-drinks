<?php

namespace App\Enums;

enum ClientType: string
{
    case Detail = 'detail';
    case Grossiste = 'grossiste';
    case Bar = 'bar';
    case Resto = 'resto';

    /**
     * Get the display label for the client type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Detail => 'Détaillant',
            self::Grossiste => 'Grossiste',
            self::Bar => 'Bar / Buvette',
            self::Resto => 'Restaurant',
        };
    }
}
