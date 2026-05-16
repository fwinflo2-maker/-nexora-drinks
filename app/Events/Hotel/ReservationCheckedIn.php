<?php

declare(strict_types=1);

namespace App\Events\Hotel;

use App\Models\Hotel\Reservation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCheckedIn
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Reservation $reservation) {}
}
