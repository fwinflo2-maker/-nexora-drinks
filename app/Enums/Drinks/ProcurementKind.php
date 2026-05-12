<?php

namespace App\Enums\Drinks;

enum ProcurementKind: string
{
    case Normal = 'normal';
    case Frigo = 'frigo';
    case CessionIn = 'cession_in';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Approvisionnement standard',
            self::Frigo => 'Approvisionnement frigo',
            self::CessionIn => 'Cession entrante',
        };
    }
}
