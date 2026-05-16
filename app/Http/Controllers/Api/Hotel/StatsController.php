<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel\Reservation;
use App\Models\Hotel\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $totalRooms = Room::count();

        return response()->json([
            'occupancy_rate' => $totalRooms > 0
                ? round(Room::where('status', 'occupied')->count() / $totalRooms * 100, 1)
                : 0,
            'rooms_available' => Room::where('status', 'available')->count(),
            'rooms_occupied' => Room::where('status', 'occupied')->count(),
            'arrivals_today' => Reservation::whereDate('check_in', today())->count(),
            'departures_today' => Reservation::whereDate('check_out', today())->count(),
            'revenue_today' => (float) Reservation::whereDate('check_in', today())
                ->whereIn('status', ['checked_in', 'checked_out'])
                ->sum('total_price'),
        ]);
    }

    public function reservations(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['guest', 'room'])
            ->latest()
            ->paginate(20);

        return response()->json($reservations);
    }
}
