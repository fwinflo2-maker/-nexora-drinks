<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hotel;

use App\Domain\HotelFnB\Services\HotelCheckoutService;
use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Enums\Hotel\ReservationStatus;
use App\Enums\Hotel\RoomStatus;
use App\Events\Hotel\ReservationCancelled;
use App\Events\Hotel\ReservationCheckedIn;
use App\Events\Hotel\ReservationCheckedOut;
use App\Exceptions\Hotel\CannotCheckoutWithOpenOrdersException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\StoreReservationRequest;
use App\Models\Hotel\Reservation;
use App\Models\Hotel\Room;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function __construct(
        private readonly HotelFnBBridgeService $bridge,
        private readonly HotelCheckoutService $checkout,
    ) {}

    public function index(Request $request, Team $current_team): Response
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $reservations = $current_team->hotelReservations()
            ->with(['room.roomType', 'guest'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('guest', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->orderByDesc('check_in')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('hotel/reservations/index', [
            'reservations' => $reservations,
            'filters' => ['status' => $status ?? '', 'search' => $search ?? ''],
            'statuses' => ReservationStatus::cases(),
        ]);
    }

    public function create(Team $current_team): Response
    {
        $rooms = $current_team->hotelRooms()
            ->with('roomType')
            ->where('status', RoomStatus::Available->value)
            ->orderBy('number')
            ->get();

        $guests = $current_team->hotelGuests()->orderBy('name')->get();

        return Inertia::render('hotel/reservations/create', [
            'rooms' => $rooms,
            'guests' => $guests,
        ]);
    }

    public function store(StoreReservationRequest $request, Team $current_team): RedirectResponse
    {
        $data = $request->validated();

        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $data['nights'] = $checkIn->diffInDays($checkOut);
        $data['status'] = ReservationStatus::Confirmed->value;

        $reservation = DB::transaction(function () use ($data, $current_team) {
            $res = $current_team->hotelReservations()->create($data);

            Room::withoutGlobalScopes()->where('id', $data['room_id'])
                ->update(['status' => RoomStatus::Reserved->value]);

            return $res;
        });

        return to_route('hotel.reservations.show', [$current_team->slug, $reservation])
            ->with('toast', ['type' => 'success', 'message' => "Réservation {$reservation->reference} créée."]);
    }

    public function show(Team $current_team, Reservation $reservation): Response
    {
        $reservation->load(['room.roomType', 'guest', 'folios', 'validator', 'canceller']);

        $fnbMenuItems = $current_team->hasModule('fnb')
            ? $current_team->fnbMenuItems()
                ->with('category')
                ->where('is_available', true)
                ->orderBy('name')
                ->get()
            : collect();

        return Inertia::render('hotel/reservations/show', [
            'reservation' => $reservation,
            'fnb_menu_items' => $fnbMenuItems,
        ]);
    }

    public function checkIn(Team $current_team, Reservation $reservation): RedirectResponse
    {
        abort_unless(
            $reservation->status === ReservationStatus::Confirmed,
            422,
            'La réservation doit être confirmée pour effectuer un check-in.'
        );

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => ReservationStatus::CheckedIn->value,
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);

            Room::withoutGlobalScopes()->where('id', $reservation->room_id)
                ->update(['status' => RoomStatus::Occupied->value]);
        });

        ReservationCheckedIn::dispatch($reservation);

        return back()->with('toast', ['type' => 'success', 'message' => 'Check-in effectué.']);
    }

    public function quickCheckOut(Team $current_team, Reservation $reservation): RedirectResponse
    {
        abort_unless(
            $reservation->status === ReservationStatus::CheckedIn,
            422,
            'La réservation doit être en cours pour effectuer un check-out.'
        );

        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => ReservationStatus::CheckedOut->value]);

            Room::withoutGlobalScopes()->where('id', $reservation->room_id)
                ->update(['status' => RoomStatus::Available->value]);
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Check-out effectué.']);
    }

    public function fnbOrders(Team $current_team, Reservation $reservation): Response
    {
        $reservation->load(['room.roomType', 'guest']);
        $fnbOrders = $reservation->fnbOrders()
            ->with(['items.menuItem', 'waiter'])
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('hotel/reservations/fnb-orders', [
            'reservation' => $reservation,
            'fnb_orders' => $fnbOrders,
            'total' => $fnbOrders->sum('total'),
        ]);
    }

    public function checkout(Request $request, Team $current_team, Reservation $reservation): RedirectResponse
    {
        abort_unless(
            $reservation->status === ReservationStatus::CheckedIn,
            422,
            'La réservation doit être en cours pour effectuer un check-out.'
        );

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'string', 'in:especes,carte,virement,mobile_money'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $this->checkout->processCheckout(
                $reservation,
                $request->only('amount', 'method', 'discount'),
                $request->user()
            );
        } catch (CannotCheckoutWithOpenOrdersException $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        ReservationCheckedOut::dispatch($reservation, (float) $request->input('amount', 0));

        return to_route('hotel.reservations.show', [$current_team->slug, $reservation])
            ->with('toast', ['type' => 'success', 'message' => 'Check-out effectué. Chambre libérée.']);
    }

    public function cancel(Team $current_team, Reservation $reservation): RedirectResponse
    {
        abort_unless(
            in_array($reservation->status, [ReservationStatus::Pending, ReservationStatus::Confirmed], strict: true),
            422,
            'Impossible d\'annuler une réservation en cours ou terminée.'
        );

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => ReservationStatus::Cancelled->value,
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
            ]);

            Room::withoutGlobalScopes()->where('id', $reservation->room_id)
                ->update(['status' => RoomStatus::Available->value]);
        });

        ReservationCancelled::dispatch($reservation);

        return back()->with('toast', ['type' => 'success', 'message' => 'Réservation annulée.']);
    }
}
