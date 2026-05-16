<?php

declare(strict_types=1);

use App\Enums\FnB\OrderStatus;
use App\Enums\Hotel\ReservationStatus;
use App\Enums\TeamRole;
use App\Models\FnB\Order;
use App\Models\Hotel\Reservation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

it('un tenant drinks-only ne peut pas accéder à /hotel/dashboard', function () {
    [$user, $team] = drinksMember(TeamRole::Admin);
    // team has no hotel module — CheckModuleAccess must block with 403

    $this->actingAs($user)
        ->get("/{$team->slug}/hotel/dashboard")
        ->assertForbidden();
});

it('un tenant hotel peut créer une réservation et la valider', function () {
    [$user, $team] = hotelMember(TeamRole::HotelReceptionist);

    $roomTypeId = DB::table('hotel_room_types')->insertGetId([
        'team_id' => $team->id,
        'name' => 'Standard',
        'base_price' => 50.00,
        'capacity' => 2,
        'amenities' => '[]',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $roomId = DB::table('hotel_rooms')->insertGetId([
        'team_id' => $team->id,
        'room_type_id' => $roomTypeId,
        'number' => '101',
        'status' => 'available',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $guestId = DB::table('hotel_guests')->insertGetId([
        'team_id' => $team->id,
        'name' => 'Alice Dupont',
        'email' => 'alice@test.com',
        'phone' => '+229 90000000',
        'id_type' => 'cni',
        'id_number' => 'CNI-TEST-001',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reservation = Reservation::create([
        'team_id' => $team->id,
        'room_id' => $roomId,
        'guest_id' => $guestId,
        'check_in' => today(),
        'check_out' => today()->addDays(2),
        'nights' => 2,
        'status' => ReservationStatus::Pending,
        'total_price' => 100.00,
    ]);

    expect($reservation->reference)->toStartWith('RES-')
        ->and($reservation->status)->toBe(ReservationStatus::Pending);

    $reservation->update([
        'status' => ReservationStatus::Confirmed,
        'validated_at' => now(),
        'validated_by' => $user->id,
    ]);

    expect($reservation->fresh()->status)->toBe(ReservationStatus::Confirmed);
});

it('un tenant fnb peut ouvrir et fermer une commande', function () {
    [$user, $team] = fnbMember(TeamRole::FnBWaiter);

    $order = Order::create([
        'team_id' => $team->id,
        'waiter_id' => $user->id,
        'status' => OrderStatus::Open,
        'total' => 0,
    ]);

    expect($order->reference)->toStartWith('CMD-')
        ->and($order->status)->toBe(OrderStatus::Open)
        ->and($order->isOpen())->toBeTrue();

    $order->update([
        'status' => OrderStatus::Closed,
        'closed_at' => now(),
    ]);

    expect($order->fresh()->status)->toBe(OrderStatus::Closed)
        ->and($order->fresh()->isOpen())->toBeFalse();
});

it('register avec modules=[hotel,fnb] active bien les deux modules', function () {
    $email = 'grand-hotel@test.example';
    Cache::put("otp_verified_{$email}", true, 600);

    $this->post(route('register.store'), [
        'name' => 'Admin Grand Hôtel',
        'company_name' => 'Grand Hôtel Test',
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'modules' => ['hotel', 'fnb'],
    ]);

    $user = User::where('email', $email)->firstOrFail();
    $team = $user->currentTeam;

    expect($team->hasModule('drinks'))->toBeTrue()
        ->and($team->hasModule('hotel'))->toBeTrue()
        ->and($team->hasModule('fnb'))->toBeTrue();
});

it('super-admin peut accéder à hotel et fnb sans module explicitement activé', function () {
    $superAdmin = User::factory()->create(['nexora_role' => 'super_admin']);
    $team = Team::factory()->create(['is_active' => true]);
    // team has NO hotel or fnb module

    $this->actingAs($superAdmin)
        ->get("/{$team->slug}/hotel/dashboard")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('hotel/Dashboard'));

    $this->actingAs($superAdmin)
        ->get("/{$team->slug}/fnb/dashboard")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('fnb/Dashboard'));
});

it('les codes RES- et CMD- sont bien auto-générés et uniques par team', function () {
    [$user, $team] = hotelMember(TeamRole::HotelReceptionist);

    $roomTypeId = DB::table('hotel_room_types')->insertGetId([
        'team_id' => $team->id, 'name' => 'Suite', 'base_price' => 100.00,
        'capacity' => 2, 'amenities' => '[]', 'is_active' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $roomId1 = DB::table('hotel_rooms')->insertGetId([
        'team_id' => $team->id, 'room_type_id' => $roomTypeId,
        'number' => '201', 'status' => 'available',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $roomId2 = DB::table('hotel_rooms')->insertGetId([
        'team_id' => $team->id, 'room_type_id' => $roomTypeId,
        'number' => '202', 'status' => 'available',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $guestId = DB::table('hotel_guests')->insertGetId([
        'team_id' => $team->id, 'name' => 'Bob Martin', 'email' => 'bob@test.com',
        'phone' => '+229 91000000', 'id_type' => 'passport', 'id_number' => 'PP-001',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $res1 = Reservation::create([
        'team_id' => $team->id, 'room_id' => $roomId1, 'guest_id' => $guestId,
        'check_in' => today(), 'check_out' => today()->addDays(1),
        'nights' => 1, 'status' => ReservationStatus::Pending, 'total_price' => 50.00,
    ]);
    $res2 = Reservation::create([
        'team_id' => $team->id, 'room_id' => $roomId2, 'guest_id' => $guestId,
        'check_in' => today()->addDays(3), 'check_out' => today()->addDays(5),
        'nights' => 2, 'status' => ReservationStatus::Pending, 'total_price' => 100.00,
    ]);

    $team->activateModule('fnb', $user);
    $order1 = Order::create(['team_id' => $team->id, 'waiter_id' => $user->id, 'status' => OrderStatus::Open, 'total' => 0]);
    $order2 = Order::create(['team_id' => $team->id, 'waiter_id' => $user->id, 'status' => OrderStatus::Open, 'total' => 0]);

    expect($res1->reference)->toStartWith('RES-')
        ->and($res2->reference)->toStartWith('RES-')
        ->and($res1->reference)->not->toBe($res2->reference)
        ->and($order1->reference)->toStartWith('CMD-')
        ->and($order2->reference)->toStartWith('CMD-')
        ->and($order1->reference)->not->toBe($order2->reference);
});
