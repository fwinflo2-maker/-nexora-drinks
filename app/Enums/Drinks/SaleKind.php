<?php

namespace App\Enums\Drinks;

enum SaleKind: string
{
    case Normal = 'normal';
    case Frigo = 'frigo';
    case CessionOut = 'cession_out';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Vente standard',
            self::Frigo => 'Vente frigo',
            self::CessionOut => 'Cession sortante',
        };
    }
}
