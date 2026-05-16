<?php

declare(strict_types=1);

use App\Domain\HotelFnB\Services\HotelCheckoutService;
use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Enums\FnB\OrderStatus;
use App\Enums\Hotel\FolioType;
use App\Enums\Hotel\ReservationStatus;
use App\Enums\Hotel\RoomStatus;
use App\Enums\TeamRole;
use App\Exceptions\Hotel\CannotCheckoutWithOpenOrdersException;
use App\Exceptions\Hotel\InvalidBridgeOperationException;
use App\Models\FnB\Order;
use App\Models\Hotel\Folio;
use App\Models\Hotel\Reservation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;

// ── Helper ────────────────────────────────────────────────────────────────────

function hotelFnBMember(TeamRole $role = TeamRole::HotelReceptionist): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['is_active' => true]);
    $team->members()->attach($user->id, ['role' => $role->value]);
    $user->update(['current_team_id' => $team->id]);
    $team->activateModule('hotel', $user);
    $team->activateModule('fnb', $user);

    return [$user->fresh(), $team->fresh()];
}

function makeCheckedInReservation(Team $team, User $user): array
{
    $roomTypeId = DB::table('hotel_room_types')->insertGetId([
        'team_id' => $team->id, 'name' => 'Standard', 'base_price' => 50000,
        'capacity' => 2, 'amenities' => '[]', 'is_active' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $roomId = DB::table('hotel_rooms')->insertGetId([
        'team_id' => $team->id, 'room_type_id' => $roomTypeId,
        'number' => '101', 'status' => RoomStatus::Occupied->value,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $guestId = DB::table('hotel_guests')->insertGetId([
        'team_id' => $team->id, 'name' => 'Alice Dupont', 'email' => 'alice@test.com',
        'phone' => '+229 90000000', 'id_type' => 'cni', 'id_number' => 'CNI-TEST-001',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $reservation = Reservation::create([
        'team_id' => $team->id,
        'room_id' => $roomId,
        'guest_id' => $guestId,
        'check_in' => today(),
        'check_out' => today()->addDays(2),
        'nights' => 2,
        'status' => ReservationStatus::CheckedIn,
        'total_price' => 100000,
    ]);

    // Add room folio
    $reservation->folios()->create([
        'team_id' => $team->id,
        'label' => 'Hébergement 2 nuits',
        'amount' => 100000,
        'type' => FolioType::Room->value,
    ]);

    return [$reservation, $roomId];
}

function makeOpenOrder(Team $team, User $user): Order
{
    return Order::create([
        'team_id' => $team->id,
        'waiter_id' => $user->id,
        'status' => OrderStatus::Open,
        'total' => 15000,
    ]);
}

// ── HotelFnBBridgeService ─────────────────────────────────────────────────────

it('attache une commande fnb à une réservation checked_in de la même team', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$reservation] = makeCheckedInReservation($team, $user);
    $order = makeOpenOrder($team, $user);

    $service = app(HotelFnBBridgeService::class);
    $service->attachOrderToReservation($order, $reservation);

    expect($order->fresh()->reservation_id)->toBe($reservation->id);
});

it('ne peut pas attacher à une réservation d\'une autre team', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$user2, $team2] = hotelFnBMember(TeamRole::HotelReceptionist);

    [$reservation] = makeCheckedInReservation($team, $user);
    $order = makeOpenOrder($team2, $user2);

    $service = app(HotelFnBBridgeService::class);

    expect(fn () => $service->attachOrderToReservation($order, $reservation))
        ->toThrow(InvalidBridgeOperationException::class);
});

it('ne peut pas attacher à une réservation non checked_in', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    $order = makeOpenOrder($team, $user);

    $roomTypeId = DB::table('hotel_room_types')->insertGetId([
        'team_id' => $team->id, 'name' => 'Standard', 'base_price' => 50000,
        'capacity' => 2, 'amenities' => '[]', 'is_active' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $roomId = DB::table('hotel_rooms')->insertGetId([
        'team_id' => $team->id, 'room_type_id' => $roomTypeId,
        'number' => '102', 'status' => 'available',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $guestId = DB::table('hotel_guests')->insertGetId([
        'team_id' => $team->id, 'name' => 'Bob Martin', 'email' => 'bob@test.com',
        'phone' => '+229 91000000', 'id_type' => 'passport', 'id_number' => 'PP-001',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $confirmed = Reservation::create([
        'team_id' => $team->id, 'room_id' => $roomId, 'guest_id' => $guestId,
        'check_in' => today(), 'check_out' => today()->addDays(1),
        'nights' => 1, 'status' => ReservationStatus::Confirmed, 'total_price' => 50000,
    ]);

    $service = app(HotelFnBBridgeService::class);

    expect(fn () => $service->attachOrderToReservation($order, $confirmed))
        ->toThrow(InvalidBridgeOperationException::class);
});

it('la clôture d\'une commande liée crée un folio restaurant', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$reservation] = makeCheckedInReservation($team, $user);
    $order = makeOpenOrder($team, $user);

    $service = app(HotelFnBBridgeService::class);
    $service->attachOrderToReservation($order, $reservation);

    $foliosBefore = Folio::where('reservation_id', $reservation->id)->count();
    $service->closeOrderAndCreateFolio($order->fresh(), $user);

    $folioAfter = Folio::where('reservation_id', $reservation->id)
        ->where('type', FolioType::Restaurant->value)
        ->first();

    expect($folioAfter)->not->toBeNull()
        ->and((float) $folioAfter->amount)->toEqual(15000.0)
        ->and($order->fresh()->status)->toBe(OrderStatus::Closed);
});

it('getReservationBalance calcule correctement room + restaurant - discounts', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$reservation] = makeCheckedInReservation($team, $user);

    // Add restaurant folio
    $reservation->folios()->create([
        'team_id' => $team->id,
        'label' => 'Dîner 1',
        'amount' => 20000,
        'type' => FolioType::Restaurant->value,
    ]);

    // Add discount
    $reservation->folios()->create([
        'team_id' => $team->id,
        'label' => 'Remise fidélité',
        'amount' => 5000,
        'type' => FolioType::Discount->value,
    ]);

    $service = app(HotelFnBBridgeService::class);
    $balance = $service->getReservationBalance($reservation->fresh());

    expect($balance['room'])->toEqual(100000.0)
        ->and($balance['restaurant'])->toEqual(20000.0)
        ->and($balance['discounts'])->toEqual(5000.0)
        ->and($balance['total'])->toEqual(115000.0)
        ->and($balance['balance'])->toEqual(115000.0);
});

it('le checkout est bloqué si des commandes fnb sont ouvertes', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$reservation] = makeCheckedInReservation($team, $user);
    $order = makeOpenOrder($team, $user);

    $bridge = app(HotelFnBBridgeService::class);
    $bridge->attachOrderToReservation($order, $reservation);

    $checkoutService = app(HotelCheckoutService::class);

    expect(fn () => $checkoutService->processCheckout(
        $reservation,
        ['amount' => 100000, 'method' => 'especes', 'discount' => null],
        $user
    ))->toThrow(CannotCheckoutWithOpenOrdersException::class);
});

it('le checkout crée un folio payment et libère la chambre', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$reservation, $roomId] = makeCheckedInReservation($team, $user);

    $checkoutService = app(HotelCheckoutService::class);
    $checkoutService->processCheckout(
        $reservation,
        ['amount' => 100000, 'method' => 'especes', 'discount' => null],
        $user
    );

    $res = $reservation->fresh();
    $room = DB::table('hotel_rooms')->where('id', $roomId)->first();

    expect($res->status)->toBe(ReservationStatus::CheckedOut)
        ->and($room->status)->toBe(RoomStatus::Maintenance->value);

    $paymentFolio = Folio::where('reservation_id', $reservation->id)
        ->where('type', FolioType::Payment->value)
        ->first();

    expect($paymentFolio)->not->toBeNull()
        ->and((float) $paymentFolio->amount)->toEqual(100000.0);
});

it('findActiveReservationByRoom trouve une réservation checked_in par numéro de chambre', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$reservation] = makeCheckedInReservation($team, $user);

    $service = app(HotelFnBBridgeService::class);
    $found = $service->findActiveReservationByRoom('101', $team->id);

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($reservation->id);
});

// ── HTTP Routes ───────────────────────────────────────────────────────────────

it('room-search retourne la réservation pour une chambre occupée', function () {
    [$user, $team] = hotelFnBMember(TeamRole::FnBWaiter);
    makeCheckedInReservation($team, $user);

    $this->actingAs($user)
        ->getJson("/{$team->slug}/fnb/room-search?room_number=101")
        ->assertOk()
        ->assertJsonFragment(['reference' => fn ($v) => str_starts_with((string) $v, 'RES-')]);
});

it('room-search retourne null si aucune chambre active', function () {
    [$user, $team] = hotelFnBMember(TeamRole::FnBWaiter);

    $this->actingAs($user)
        ->getJson("/{$team->slug}/fnb/room-search?room_number=999")
        ->assertOk()
        ->assertJson(['reservation' => null]);
});

it('un tenant sans module hotel ne peut pas accéder à room-search', function () {
    [$user, $team] = fnbMember(TeamRole::FnBWaiter);

    $this->actingAs($user)
        ->getJson("/{$team->slug}/fnb/room-search?room_number=101")
        ->assertForbidden();
});

it('room-service crée une commande liée à la réservation', function () {
    [$user, $team] = hotelFnBMember(TeamRole::FnBWaiter);
    [$reservation] = makeCheckedInReservation($team, $user);

    $menuItemId = DB::table('fnb_menu_items')->insertGetId([
        'team_id' => $team->id, 'name' => 'Sandwich Club', 'price' => 3500,
        'is_available' => 1, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->post("/{$team->slug}/fnb/room-service", [
            'reservation_id' => $reservation->id,
            'items' => [['menu_item_id' => $menuItemId, 'quantity' => 2]],
        ])
        ->assertRedirect();

    $order = Order::withoutGlobalScopes()
        ->where('team_id', $team->id)
        ->where('reservation_id', $reservation->id)
        ->first();

    expect($order)->not->toBeNull()
        ->and($order->order_type->value ?? $order->order_type)->toBe('room_service')
        ->and((float) $order->total)->toEqual(7000.0);
});

it('le folio summary retourne les données consolidées', function () {
    [$user, $team] = hotelFnBMember(TeamRole::HotelReceptionist);
    [$reservation] = makeCheckedInReservation($team, $user);

    $this->actingAs($user)
        ->get("/{$team->slug}/hotel/reservations/{$reservation->id}/folio")
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('hotel/reservations/folio-summary')
            ->has('balance')
            ->has('folios')
        );
});
