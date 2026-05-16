<?php

declare(strict_types=1);

namespace App\Enums\FnB;

enum OrderStatus: string
{
    case Open = 'open';
    case Sent = 'sent';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Ouverte',
            self::Sent => 'Envoyée',
            self::Preparing => 'En préparation',
            self::Ready => 'Prête',
            self::Closed => 'Clôturée',
            self::Cancelled => 'Annulée',
        };
    }
}
