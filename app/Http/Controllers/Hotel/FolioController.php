<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hotel;

use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\StoreFolioRequest;
use App\Models\Hotel\Folio;
use App\Models\Hotel\Reservation;
use App\Models\Team;
use App\Services\Hotel\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class FolioController extends Controller
{
    public function __construct(
        private readonly HotelFnBBridgeService $bridge,
        private readonly PdfService $pdfService,
    ) {}

    public function store(StoreFolioRequest $request, Team $current_team, Reservation $reservation): RedirectResponse
    {
        $reservation->folios()->create(array_merge(
            $request->validated(),
            ['team_id' => $current_team->id]
        ));

        return back()->with('toast', ['type' => 'success', 'message' => 'Ligne ajoutée au folio.']);
    }

    public function destroy(Team $current_team, Reservation $reservation, Folio $folio): RedirectResponse
    {
        $folio->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Ligne supprimée.']);
    }

    public function summary(Team $current_team, Reservation $reservation): InertiaResponse
    {
        $reservation->load(['room.roomType', 'guest', 'folios', 'fnbOrders.items.menuItem']);

        $balance = $this->bridge->getReservationBalance($reservation);

        return Inertia::render('hotel/reservations/folio-summary', [
            'reservation' => $reservation,
            'balance' => $balance,
            'folios' => $reservation->folios()->orderBy('created_at')->get(),
            'fnb_orders' => $reservation->fnbOrders()->with('items.menuItem')->get(),
        ]);
    }

    public function pdf(Team $current_team, Reservation $reservation): Response
    {
        $reservation->load(['room.roomType', 'guest', 'folios', 'fnbOrders.items.menuItem']);

        $balance = $this->bridge->getReservationBalance($reservation);

        return $this->pdfService->render(
            'hotel.pdf.folio',
            [
                'reservation' => $reservation,
                'balance' => $balance,
                'folios' => $reservation->folios()->orderBy('created_at')->get(),
                'fnbOrders' => $reservation->fnbOrders()->with('items.menuItem')->get(),
                'team' => $current_team,
            ]
        );
    }
}
