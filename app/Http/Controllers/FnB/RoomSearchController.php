<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomSearchController extends Controller
{
    public function __construct(private readonly HotelFnBBridgeService $bridge) {}

    public function search(Request $request, Team $current_team): JsonResponse
    {
        abort_unless($current_team->hasModule('hotel'), 403, 'Module Hotel non activé.');

        $request->validate(['room_number' => ['required', 'string', 'max:20']]);

        $reservation = $this->bridge->findActiveReservationByRoom(
            $request->string('room_number'),
            $current_team->id
        );

        if ($reservation === null) {
            return response()->json(['reservation' => null]);
        }

        return response()->json([
            'reservation' => [
                'id' => $reservation->id,
                'reference' => $reservation->reference,
                'check_in' => $reservation->check_in->format('d/m/Y'),
                'check_out' => $reservation->check_out->format('d/m/Y'),
                'room' => [
                    'number' => $reservation->room->number,
                    'type' => $reservation->room->roomType->name,
                ],
                'guest' => [
                    'name' => $reservation->guest->name,
                    'phone' => $reservation->guest->phone,
                ],
                'pending_fnb_total' => $this->bridge->openFnBTotal($reservation),
            ],
        ]);
    }
}
