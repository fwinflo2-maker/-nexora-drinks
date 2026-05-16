<?php

declare(strict_types=1);

namespace App\Enums\FnB;

enum OrderType: string
{
    case Table = 'table';
    case RoomService = 'room_service';
    case Takeaway = 'takeaway';

    public function label(): string
    {
        return match ($this) {
            self::Table => 'Sur table',
            self::RoomService => 'Room service',
            self::Takeaway => 'À emporter',
        };
    }
}
