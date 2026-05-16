<?php

declare(strict_types=1);

namespace App\Listeners\Hotel;

use App\Events\Hotel\ReservationCancelled;
use App\Models\FnB\Order;

class DetachLinkedFnBOrders
{
    public function handle(ReservationCancelled $event): void
    {
        Order::withoutGlobalScopes()
            ->where('reservation_id', $event->reservation->id)
            ->update(['reservation_id' => null]);
    }
}
