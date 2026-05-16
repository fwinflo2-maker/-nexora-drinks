<?php

declare(strict_types=1);

namespace App\Listeners\FnB;

use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Enums\Hotel\FolioType;
use App\Events\FnB\OrderClosed;
use App\Models\Hotel\Reservation;

class CreateFolioIfLinkedToReservation
{
    public function __construct(private readonly HotelFnBBridgeService $bridge) {}

    public function handle(OrderClosed $event): void
    {
        $order = $event->order;

        if ($order->reservation_id === null) {
            return;
        }

        $reservation = Reservation::withoutGlobalScopes()->find($order->reservation_id);

        if ($reservation === null) {
            return;
        }

        $alreadyExists = $reservation->folios()
            ->where('type', FolioType::Restaurant->value)
            ->where('label', 'like', "%{$order->reference}%")
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $reservation->folios()->create([
            'team_id' => $reservation->team_id,
            'label' => "Restaurant — commande {$order->reference}",
            'amount' => $order->total,
            'type' => FolioType::Restaurant->value,
        ]);
    }
}
