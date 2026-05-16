<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel\Reservation;
use App\Models\Hotel\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $request->user()->currentTeam;

        $stats = [
            'rooms_available' => Room::where('status', 'available')->count(),
            'rooms_occupied' => Room::where('status', 'occupied')->count(),
            'rooms_maintenance' => Room::where('status', 'maintenance')->count(),
            'reservations_today' => Reservation::whereDate('check_in', today())
                ->orWhereDate('check_out', today())
                ->count(),
        ];

        $arrivalsToday = Reservation::with(['guest', 'room.roomType'])
            ->whereDate('check_in', today())
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'reference' => $r->reference,
                'guest_name' => $r->guest?->name,
                'room_number' => $r->room?->number,
                'status' => $r->status,
            ]);

        $departuresToday = Reservation::with(['guest', 'room'])
            ->whereDate('check_out', today())
            ->where('status', 'checked_in')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'reference' => $r->reference,
                'guest_name' => $r->guest?->name,
                'room_number' => $r->room?->number,
                'status' => $r->status,
            ]);

        $recentReservations = Reservation::with(['guest', 'room'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'reference' => $r->reference,
                'guest_name' => $r->guest?->name,
                'room_number' => $r->room?->number,
                'status' => $r->status,
                'total_price' => $r->total_price,
                'check_in' => $r->check_in?->format('d/m/Y'),
                'check_out' => $r->check_out?->format('d/m/Y'),
            ]);

        $revenueChart = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'date' => $date->format('d/m'),
                'revenue' => (float) Reservation::whereDate('check_in', $date)
                    ->whereIn('status', ['checked_in', 'checked_out'])
                    ->sum('total_price'),
            ];
        })->values();

        return Inertia::render('hotel/Dashboard', compact(
            'stats',
            'arrivalsToday',
            'departuresToday',
            'recentReservations',
            'revenueChart',
        ));
    }
}
