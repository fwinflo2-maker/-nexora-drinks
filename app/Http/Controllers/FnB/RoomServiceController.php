<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Enums\FnB\OrderStatus;
use App\Enums\FnB\OrderType;
use App\Enums\Hotel\ReservationStatus;
use App\Events\FnB\RoomServiceOrdered;
use App\Http\Controllers\Controller;
use App\Models\Hotel\Reservation;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomServiceController extends Controller
{
    public function store(Request $request, Team $current_team): RedirectResponse
    {
        abort_unless($current_team->hasModule('hotel'), 403, 'Module Hotel non activé.');

        $request->validate([
            'reservation_id' => ['required', 'integer'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $reservation = Reservation::withoutGlobalScopes()
            ->where('team_id', $current_team->id)
            ->where('status', ReservationStatus::CheckedIn->value)
            ->findOrFail($request->integer('reservation_id'));

        $order = DB::transaction(function () use ($request, $current_team, $reservation) {
            $order = $current_team->fnbOrders()->create([
                'waiter_id' => $request->user()->id,
                'reservation_id' => $reservation->id,
                'order_type' => OrderType::RoomService->value,
                'table_id' => null,
                'status' => OrderStatus::Open->value,
                'notes' => $request->input('notes'),
                'total' => 0,
            ]);

            $total = 0.0;
            foreach ($request->input('items') as $itemData) {
                $menuItem = $current_team->fnbMenuItems()->findOrFail($itemData['menu_item_id']);
                $unitPrice = (float) $menuItem->price;

                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'status' => 'pending',
                    'notes' => $itemData['notes'] ?? null,
                ]);

                $total += $unitPrice * $itemData['quantity'];
            }

            $order->update(['total' => $total]);

            return $order;
        });

        RoomServiceOrdered::dispatch($order, $reservation);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Room service {$order->reference} créé pour chambre {$reservation->room->number}.",
        ]);
    }
}
