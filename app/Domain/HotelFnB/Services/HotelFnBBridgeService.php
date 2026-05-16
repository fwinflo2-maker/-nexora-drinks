<?php

declare(strict_types=1);

namespace App\Domain\HotelFnB\Services;

use App\Enums\FnB\OrderStatus;
use App\Enums\Hotel\FolioType;
use App\Enums\Hotel\ReservationStatus;
use App\Enums\Hotel\RoomStatus;
use App\Exceptions\Hotel\InvalidBridgeOperationException;
use App\Models\FnB\Order;
use App\Models\Hotel\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HotelFnBBridgeService
{
    /**
     * Rattache une commande F&B à une réservation hôtel.
     * Vérifie : même team, réservation checked_in, commande active, pas déjà rattachée.
     */
    public function attachOrderToReservation(Order $order, Reservation $reservation): void
    {
        if ($order->team_id !== $reservation->team_id) {
            throw new InvalidBridgeOperationException('La commande et la réservation doivent appartenir au même compte.');
        }

        if ($reservation->status !== ReservationStatus::CheckedIn) {
            throw new InvalidBridgeOperationException('La réservation doit être en cours (check-in effectué).');
        }

        if (in_array($order->status, [OrderStatus::Closed, OrderStatus::Cancelled], strict: true)) {
            throw new InvalidBridgeOperationException('Impossible de rattacher une commande clôturée ou annulée.');
        }

        if ($order->reservation_id !== null && $order->reservation_id !== $reservation->id) {
            throw new InvalidBridgeOperationException('La commande est déjà rattachée à une autre réservation.');
        }

        $order->update(['reservation_id' => $reservation->id]);
    }

    /**
     * Détache une commande F&B de sa réservation.
     */
    public function detachOrderFromReservation(Order $order): void
    {
        if (in_array($order->status, [OrderStatus::Closed, OrderStatus::Cancelled], strict: true)) {
            throw new InvalidBridgeOperationException('Impossible de détacher une commande clôturée ou annulée.');
        }

        $order->update(['reservation_id' => null]);
    }

    /**
     * Clôture une commande et crée un folio restaurant si elle est rattachée à une réservation.
     * Atomique (DB::transaction).
     */
    public function closeOrderAndCreateFolio(Order $order, User $by): void
    {
        DB::transaction(function () use ($order, $by) {
            $order->update([
                'status' => OrderStatus::Closed->value,
                'closed_at' => now(),
                'validated_at' => now(),
                'validated_by' => $by->id,
            ]);

            if ($order->reservation_id !== null) {
                $reservation = Reservation::withoutGlobalScopes()->findOrFail($order->reservation_id);

                $reservation->folios()->create([
                    'team_id' => $reservation->team_id,
                    'label' => "Restaurant — commande {$order->reference}",
                    'amount' => $order->total,
                    'type' => FolioType::Restaurant->value,
                ]);
            }
        });
    }

    /**
     * Calcule le solde consolidé d'une réservation (hébergement + restaurant + extras).
     *
     * @return array{room: float, restaurant: float, extras: float, discounts: float, total: float, paid: float, balance: float}
     */
    public function getReservationBalance(Reservation $reservation): array
    {
        $folios = $reservation->folios()->get();

        $room = (float) $folios->where('type', FolioType::Room)->sum('amount');
        $restaurant = (float) $folios->where('type', FolioType::Restaurant)->sum('amount');
        $extras = (float) $folios->whereIn('type', [FolioType::Service, FolioType::Extra])->sum('amount');
        $discounts = (float) $folios->where('type', FolioType::Discount)->sum('amount');
        $payments = (float) $folios->where('type', FolioType::Payment)->sum('amount');

        $total = $room + $restaurant + $extras - $discounts;
        $paid = (float) $reservation->paid_amount + $payments;

        return [
            'room' => $room,
            'restaurant' => $restaurant,
            'extras' => $extras,
            'discounts' => $discounts,
            'total' => $total,
            'paid' => $paid,
            'balance' => max(0.0, $total - $paid),
        ];
    }

    /**
     * Trouve la réservation checked_in pour un numéro de chambre donné.
     */
    public function findActiveReservationByRoom(string $roomNumber, int $teamId): ?Reservation
    {
        return Reservation::withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->where('status', ReservationStatus::CheckedIn->value)
            ->whereHas('room', fn ($q) => $q->where('number', $roomNumber))
            ->with(['room.roomType', 'guest', 'fnbOrders' => fn ($q) => $q->active()])
            ->first();
    }

    /**
     * Retourne le total des commandes F&B ouvertes rattachées à une réservation.
     */
    public function openFnBTotal(Reservation $reservation): float
    {
        return (float) $reservation->fnbOrders()->active()->sum('total');
    }

    /**
     * Vérifie si la réservation a des commandes F&B encore ouvertes.
     */
    public function hasOpenFnBOrders(Reservation $reservation): bool
    {
        return $reservation->fnbOrders()->active()->exists();
    }

    /**
     * Libère la chambre et passe son statut en maintenance après check-out.
     */
    public function releaseRoomAfterCheckout(Reservation $reservation): void
    {
        $reservation->room()->withoutGlobalScopes()
            ->where('id', $reservation->room_id)
            ->update(['status' => RoomStatus::Maintenance->value]);
    }
}
