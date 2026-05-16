<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\StoreRoomTypeRequest;
use App\Http\Requests\Hotel\UpdateRoomTypeRequest;
use App\Models\Hotel\RoomType;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoomTypeController extends Controller
{
    public function index(Team $current_team): Response
    {
        $roomTypes = $current_team->hotelRoomTypes()
            ->withCount('rooms')
            ->orderBy('name')
            ->get();

        return Inertia::render('hotel/room-types/index', [
            'roomTypes' => $roomTypes,
        ]);
    }

    public function create(Team $current_team): Response
    {
        return Inertia::render('hotel/room-types/create');
    }

    public function store(StoreRoomTypeRequest $request, Team $current_team): RedirectResponse
    {
        $current_team->hotelRoomTypes()->create($request->validated());

        return to_route('hotel.room-types.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Type de chambre créé.']);
    }

    public function edit(Team $current_team, RoomType $roomType): Response
    {
        return Inertia::render('hotel/room-types/edit', [
            'roomType' => $roomType,
        ]);
    }

    public function update(UpdateRoomTypeRequest $request, Team $current_team, RoomType $roomType): RedirectResponse
    {
        $roomType->update($request->validated());

        return to_route('hotel.room-types.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Type de chambre mis à jour.']);
    }

    public function destroy(Team $current_team, RoomType $roomType): RedirectResponse
    {
        $roomType->delete();

        return to_route('hotel.room-types.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Type de chambre supprimé.']);
    }
}
