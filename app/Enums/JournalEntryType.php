<?php

namespace App\Enums;

enum JournalEntryType: string
{
    case Sale = 'sale';
    case Purchase = 'purchase';
    case PaymentIn = 'payment_in';
    case PaymentOut = 'payment_out';
    case StockIn = 'stock_in';
    case StockOut = 'stock_out';
    case ConsignmentOut = 'consignment_out';
    case ConsignmentIn = 'consignment_in';
    case Expense = 'expense';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'Vente',
            self::Purchase => 'Achat',
            self::PaymentIn => 'Encaissement',
            self::PaymentOut => 'Décaissement',
            self::StockIn => 'Entrée de stock',
            self::StockOut => 'Sortie de stock',
            self::ConsignmentOut => 'Consignation sortante',
            self::ConsignmentIn => 'Retour de consignation',
            self::Expense => 'Dépense',
            self::Adjustment => 'Ajustement',
        };
    }

    public function isInflow(): bool
    {
        return match ($this) {
            self::Sale, self::PaymentIn, self::ConsignmentIn => true,
            default => false,
        };
    }

    public function defaultDebitAccount(): string
    {
        return match ($this) {
            self::Sale => '411',
            self::Purchase => '31',
            self::PaymentIn => '571',
            self::PaymentOut => '401',
            self::StockIn => '31',
            self::StockOut => '601',
            self::ConsignmentOut => '413',
            self::ConsignmentIn => '471',
            self::Expense => '6',
            self::Adjustment => '471',
        };
    }

    public function defaultCreditAccount(): string
    {
        return match ($this) {
            self::Sale => '701',
            self::Purchase => '401',
            self::PaymentIn => '411',
            self::PaymentOut => '571',
            self::StockIn => '401',
            self::StockOut => '31',
            self::ConsignmentOut => '701',
            self::ConsignmentIn => '413',
            self::Expense => '571',
            self::Adjustment => '471',
        };
    }
}
