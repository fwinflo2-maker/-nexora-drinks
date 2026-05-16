<?php

declare(strict_types=1);

namespace App\Services\Hotel;

use App\Enums\Hotel\ReservationStatus;
use App\Models\Hotel\Reservation;
use App\Models\Hotel\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /** @return array<int, array<string, mixed>> */
    public function reservationsReport(int $teamId, string $dateFrom, string $dateTo): array
    {
        return Reservation::with(['guest', 'room.roomType'])
            ->where('team_id', $teamId)
            ->whereBetween('check_in', [$dateFrom, $dateTo])
            ->orderBy('check_in')
            ->get()
            ->map(fn (Reservation $r) => [
                'reference' => $r->reference,
                'guest_name' => $r->guest?->name ?? '—',
                'room_number' => $r->room?->number ?? '—',
                'room_type' => $r->room?->roomType?->name ?? '—',
                'check_in' => $r->check_in?->format('d/m/Y') ?? '—',
                'check_out' => $r->check_out?->format('d/m/Y') ?? '—',
                'nights' => $r->nights ?? 0,
                'total_price' => (float) $r->total_price,
                'status' => $r->status instanceof ReservationStatus ? $r->status->value : $r->status,
            ])
            ->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    public function revenueReport(int $teamId, string $dateFrom, string $dateTo): array
    {
        $rows = Reservation::select(
            DB::raw('DATE(check_in) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_price) as revenue'),
        )
            ->where('team_id', $teamId)
            ->whereBetween('check_in', [$dateFrom, $dateTo])
            ->whereIn('status', [
                ReservationStatus::Confirmed->value,
                ReservationStatus::CheckedIn->value,
                ReservationStatus::CheckedOut->value,
            ])
            ->groupBy(DB::raw('DATE(check_in)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => Carbon::parse($row->date)->format('d/m/Y'),
                'count' => (int) $row->count,
                'revenue' => (float) $row->revenue,
            ])
            ->toArray();

        return $rows;
    }

    /** @return array<int, array<string, mixed>> */
    public function occupancyReport(int $teamId, string $dateFrom, string $dateTo): array
    {
        $roomTypes = DB::table('hotel_room_types')
            ->where('team_id', $teamId)
            ->get();

        $totalRooms = Room::where('team_id', $teamId)->count();
        $days = max(1, Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1);

        return $roomTypes->map(function ($rt) use ($teamId, $dateFrom, $dateTo, $days) {
            $roomCount = Room::where('team_id', $teamId)
                ->where('room_type_id', $rt->id)
                ->count();

            $occupiedNights = Reservation::whereHas('room', fn ($q) => $q->where('room_type_id', $rt->id))
                ->where('team_id', $teamId)
                ->whereBetween('check_in', [$dateFrom, $dateTo])
                ->whereIn('status', [
                    ReservationStatus::CheckedIn->value,
                    ReservationStatus::CheckedOut->value,
                ])
                ->sum('nights');

            $capacity = $roomCount * $days;
            $rate = $capacity > 0 ? round((int) $occupiedNights / $capacity * 100, 1) : 0.0;

            return [
                'room_type' => $rt->name,
                'room_count' => $roomCount,
                'occupied_nights' => (int) $occupiedNights,
                'capacity_nights' => $capacity,
                'occupancy_rate' => $rate,
            ];
        })->toArray();
    }
}
