<?php

namespace App\Enums\Drinks;

enum StockMovementKind: string
{
    case ProcurementIn = 'procurement_in';
    case SaleOut = 'sale_out';
    case CessionIn = 'cession_in';
    case CessionOut = 'cession_out';
    case FrigoIn = 'frigo_in';
    case FrigoOut = 'frigo_out';
    case InventoryAdjust = 'inventory_adjust';
    case Loss = 'loss';
    case ConsignmentOut = 'consignment_out';
    case ConsignmentReturn = 'consignment_return';

    public function label(): string
    {
        return match ($this) {
            self::ProcurementIn => 'Entrée approvisionnement',
            self::SaleOut => 'Sortie vente',
            self::CessionIn => 'Cession reçue',
            self::CessionOut => 'Cession émise',
            self::FrigoIn => 'Entrée frigo',
            self::FrigoOut => 'Sortie frigo',
            self::InventoryAdjust => 'Ajustement inventaire',
            self::Loss => 'Perte',
            self::ConsignmentOut => 'Consignation sortante',
            self::ConsignmentReturn => 'Retour consignation',
        };
    }

    public function isPositive(): bool
    {
        return match ($this) {
            self::ProcurementIn, self::CessionIn, self::FrigoIn, self::ConsignmentReturn => true,
            self::SaleOut, self::CessionOut, self::FrigoOut, self::Loss, self::ConsignmentOut => false,
            self::InventoryAdjust => false, // signe selon le delta
        };
    }
}
