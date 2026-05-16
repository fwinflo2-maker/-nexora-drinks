<?php

declare(strict_types=1);

namespace App\Listeners\FnB;

use App\Events\FnB\RoomServiceOrdered;
use Illuminate\Support\Facades\Log;

class NotifyReceptionDashboard
{
    public function handle(RoomServiceOrdered $event): void
    {
        Log::info('Room service ordered — reception notified.', [
            'team_id' => $event->order->team_id,
            'order' => $event->order->reference,
            'reservation' => $event->reservation->reference,
            'room' => $event->reservation->room?->number,
            'total' => $event->order->total,
        ]);
    }
}
