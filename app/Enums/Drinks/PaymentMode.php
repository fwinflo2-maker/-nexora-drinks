<?php

namespace App\Enums\Drinks;

enum PaymentMode: string
{
    case Cash = 'cash';
    case Cheque = 'cheque';
    case MobileMoney = 'mobile_money';
    case BankTransfer = 'bank_transfer';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::Cheque => 'Chèque',
            self::MobileMoney => 'Mobile Money',
            self::BankTransfer => 'Virement bancaire',
            self::Other => 'Autre',
        };
    }
}
