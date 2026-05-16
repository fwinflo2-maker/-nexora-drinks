<?php

declare(strict_types=1);

namespace App\Listeners\Hotel;

use App\Events\Hotel\ReservationCheckedIn;
use Illuminate\Support\Facades\Log;

class NotifyFnBModuleOfNewGuest
{
    public function handle(ReservationCheckedIn $event): void
    {
        $reservation = $event->reservation;

        if (! $reservation->relationLoaded('guest')) {
            $reservation->load('guest', 'room');
        }

        Log::info('Hotel: guest checked in — F&B module notified.', [
            'team_id' => $reservation->team_id,
            'reservation' => $reservation->reference,
            'guest' => $reservation->guest->name,
            'room' => $reservation->room->number,
        ]);
    }
}
