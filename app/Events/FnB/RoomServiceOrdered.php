<?php

declare(strict_types=1);

namespace App\Events\FnB;

use App\Models\FnB\Order;
use App\Models\Hotel\Reservation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomServiceOrdered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly Reservation $reservation,
    ) {}
}
