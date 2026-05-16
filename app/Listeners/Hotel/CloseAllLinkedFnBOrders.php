<?php

declare(strict_types=1);

namespace App\Listeners\Hotel;

use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Enums\FnB\OrderStatus;
use App\Events\Hotel\ReservationCheckedOut;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class CloseAllLinkedFnBOrders implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(private readonly HotelFnBBridgeService $bridge) {}

    public function handle(ReservationCheckedOut $event): void
    {
        $reservation = $event->reservation;

        $openOrders = $reservation->fnbOrders()
            ->whereIn('status', [
                OrderStatus::Open->value,
                OrderStatus::Sent->value,
                OrderStatus::Preparing->value,
                OrderStatus::Ready->value,
            ])
            ->get();

        $systemUser = User::withoutGlobalScopes()
            ->where('nexora_role', 'super_admin')
            ->first();

        foreach ($openOrders as $order) {
            $this->bridge->closeOrderAndCreateFolio($order, $systemUser ?? $order->waiter);
        }
    }
}
