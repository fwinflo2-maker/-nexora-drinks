<?php

declare(strict_types=1);

namespace App\Domain\HotelFnB\Services;

use App\Enums\Hotel\FolioType;
use App\Enums\Hotel\ReservationStatus;
use App\Exceptions\Hotel\CannotCheckoutWithOpenOrdersException;
use App\Models\Hotel\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HotelCheckoutService
{
    public function __construct(private readonly HotelFnBBridgeService $bridge) {}

    /**
     * Traite le check-out unifié (hébergement + F&B).
     *
     * @param  array{amount: float, method: string, discount?: float}  $paymentData
     *
     * @throws CannotCheckoutWithOpenOrdersException
     */
    public function processCheckout(Reservation $reservation, array $paymentData, User $by): void
    {
        $openCount = $reservation->fnbOrders()->active()->count();

        if ($openCount > 0) {
            throw new CannotCheckoutWithOpenOrdersException($openCount);
        }

        DB::transaction(function () use ($reservation, $paymentData, $by) {
            $balance = $this->bridge->getReservationBalance($reservation);

            $paid = (float) ($paymentData['amount'] ?? $balance['balance']);
            $discount = (float) ($paymentData['discount'] ?? 0.0);

            if ($discount > 0) {
                $reservation->folios()->create([
                    'team_id' => $reservation->team_id,
                    'label' => 'Remise check-out',
                    'amount' => $discount,
                    'type' => FolioType::Discount->value,
                ]);
            }

            $reservation->folios()->create([
                'team_id' => $reservation->team_id,
                'label' => "Paiement — {$paymentData['method']}",
                'amount' => $paid,
                'type' => FolioType::Payment->value,
            ]);

            $reservation->update([
                'status' => ReservationStatus::CheckedOut->value,
                'paid_amount' => (float) $reservation->paid_amount + $paid,
                'validated_at' => now(),
                'validated_by' => $by->id,
            ]);

            $this->bridge->releaseRoomAfterCheckout($reservation);
        });
    }
}
