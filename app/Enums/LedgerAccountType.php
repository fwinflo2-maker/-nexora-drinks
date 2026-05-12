<?php

namespace App\Enums;

enum LedgerAccountType: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Asset => 'Actif',
            self::Liability => 'Passif',
            self::Equity => 'Capitaux propres',
            self::Revenue => 'Produit',
            self::Expense => 'Charge',
        };
    }

    public function normalBalance(): string
    {
        return match ($this) {
            self::Asset, self::Expense => 'debit',
            self::Liability, self::Equity, self::Revenue => 'credit',
        };
    }
}
