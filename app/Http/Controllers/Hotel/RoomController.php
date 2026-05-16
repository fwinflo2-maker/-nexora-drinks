<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hotel;

use App\Enums\Hotel\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\StoreRoomRequest;
use App\Http\Requests\Hotel\UpdateRoomRequest;
use App\Models\Hotel\Room;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(Team $current_team): Response
    {
        $rooms = $current_team->hotelRooms()
            ->with('roomType')
            ->orderBy('number')
            ->get();

        return Inertia::render('hotel/rooms/index', [
            'rooms' => $rooms,
            'statuses' => RoomStatus::cases(),
        ]);
    }

    public function create(Team $current_team): Response
    {
        $roomTypes = $current_team->hotelRoomTypes()->where('is_active', true)->orderBy('name')->get();

        return Inertia::render('hotel/rooms/create', [
            'roomTypes' => $roomTypes,
            'statuses' => RoomStatus::cases(),
        ]);
    }

    public function store(StoreRoomRequest $request, Team $current_team): RedirectResponse
    {
        $current_team->hotelRooms()->create($request->validated());

        return to_route('hotel.rooms.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Chambre créée.']);
    }

    public function edit(Team $current_team, Room $room): Response
    {
        $roomTypes = $current_team->hotelRoomTypes()->where('is_active', true)->orderBy('name')->get();

        return Inertia::render('hotel/rooms/edit', [
            'room' => $room->load('roomType'),
            'roomTypes' => $roomTypes,
            'statuses' => RoomStatus::cases(),
        ]);
    }

    public function update(UpdateRoomRequest $request, Team $current_team, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return to_route('hotel.rooms.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Chambre mise à jour.']);
    }

    public function destroy(Team $current_team, Room $room): RedirectResponse
    {
        $room->delete();

        return to_route('hotel.rooms.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Chambre supprimée.']);
    }
}
