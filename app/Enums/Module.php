<?php

declare(strict_types=1);

namespace App\Enums;

enum Module: string
{
    case Drinks = 'drinks';
    case Hotel = 'hotel';
    case FnB = 'fnb';

    public function label(): string
    {
        return match ($this) {
            self::Drinks => 'Distribution boissons',
            self::Hotel => 'Hôtellerie',
            self::FnB => 'Restauration F&B',
        };
    }

    /** @return array<array{value: string, label: string}> */
    public static function options(): array
    {
        return array_map(
            fn (self $m) => ['value' => $m->value, 'label' => $m->label()],
            self::cases()
        );
    }
}
