<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\StoreGuestRequest;
use App\Http\Requests\Hotel\UpdateGuestRequest;
use App\Models\Hotel\Guest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{
    public function index(Request $request, Team $current_team): Response
    {
        $search = $request->get('search');

        $guests = $current_team->hotelGuests()
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->withCount('reservations')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('hotel/guests/index', [
            'guests' => $guests,
            'filters' => ['search' => $search ?? ''],
        ]);
    }

    public function show(Team $current_team, Guest $guest): Response
    {
        $guest->load(['reservations.room.roomType']);

        return Inertia::render('hotel/guests/show', [
            'guest' => $guest,
        ]);
    }

    public function create(Team $current_team): Response
    {
        return Inertia::render('hotel/guests/create');
    }

    public function store(StoreGuestRequest $request, Team $current_team): RedirectResponse
    {
        $guest = $current_team->hotelGuests()->create($request->validated());

        return to_route('hotel.guests.show', [$current_team->slug, $guest])
            ->with('toast', ['type' => 'success', 'message' => 'Client créé.']);
    }

    public function edit(Team $current_team, Guest $guest): Response
    {
        return Inertia::render('hotel/guests/edit', [
            'guest' => $guest,
        ]);
    }

    public function update(UpdateGuestRequest $request, Team $current_team, Guest $guest): RedirectResponse
    {
        $guest->update($request->validated());

        return to_route('hotel.guests.show', [$current_team->slug, $guest])
            ->with('toast', ['type' => 'success', 'message' => 'Client mis à jour.']);
    }

    public function destroy(Team $current_team, Guest $guest): RedirectResponse
    {
        $guest->delete();

        return to_route('hotel.guests.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Client supprimé.']);
    }
}
