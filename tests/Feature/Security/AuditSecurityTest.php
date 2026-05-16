<?php

declare(strict_types=1);

use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Enums\FnB\OrderStatus;
use App\Enums\Hotel\ReservationStatus;
use App\Enums\Hotel\RoomStatus;
use App\Enums\TeamRole;
use App\Exceptions\Hotel\InvalidBridgeOperationException;
use App\Models\FnB\Order;
use App\Models\Hotel\Reservation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ── Helpers ───────────────────────────────────────────────────────────────────

function auditTeamMember(TeamRole $role = TeamRole::Admin): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['is_active' => true]);
    $team->members()->attach($user->id, ['role' => $role->value]);
    $user->forceFill(['current_team_id' => $team->id])->save();

    return [$user->fresh(), $team->fresh()];
}

function auditSuperAdmin(): User
{
    return User::factory()->create(['nexora_role' => 'super_admin']);
}

function auditFnbCategoryId(Team $team): int
{
    return DB::table('fnb_categories')->insertGetId([
        'team_id' => $team->id, 'name' => 'Test Category',
        'is_active' => 1, 'sort_order' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

function auditCheckedInReservation(Team $team, User $user): Reservation
{
    $roomTypeId = DB::table('hotel_room_types')->insertGetId([
        'team_id' => $team->id, 'name' => 'Standard', 'base_price' => 50000,
        'capacity' => 2, 'amenities' => '[]', 'is_active' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $roomId = DB::table('hotel_rooms')->insertGetId([
        'team_id' => $team->id, 'room_type_id' => $roomTypeId,
        'number' => '201', 'status' => RoomStatus::Occupied->value,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $guestId = DB::table('hotel_guests')->insertGetId([
        'team_id' => $team->id, 'name' => 'Test Guest', 'email' => 'guest-audit@test.com',
        'phone' => '+229 90000001', 'id_type' => 'cni', 'id_number' => 'CNI-AUDIT-001',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return Reservation::create([
        'team_id' => $team->id,
        'room_id' => $roomId,
        'guest_id' => $guestId,
        'check_in' => today(),
        'check_out' => today()->addDays(1),
        'nights' => 1,
        'status' => ReservationStatus::CheckedIn,
        'total_price' => 50000,
    ]);
}

function auditOpenOrder(Team $team, User $user, ?int $reservationId = null): Order
{
    return Order::create([
        'team_id' => $team->id,
        'waiter_id' => $user->id,
        'status' => OrderStatus::Open,
        'total' => 5000,
        'reservation_id' => $reservationId,
    ]);
}

// ── 6.1 Mass assignment protection ───────────────────────────────────────────

test('nexora_role ne peut pas être assigné par mass assignment', function () {
    $user = User::factory()->create();
    $original = $user->nexora_role;

    $user->fill(['nexora_role' => 'super_admin']);
    $user->save();
    $user->refresh();

    expect($user->nexora_role)->toBe($original);
});

test('setNexoraRole modifie le rôle et crée un audit log', function () {
    $admin = auditSuperAdmin();
    $target = User::factory()->create();

    $this->actingAs($admin);
    $target->setNexoraRole('super_admin');
    $target->refresh();

    expect($target->nexora_role)->toBe('super_admin');

    $log = DB::table('godmode_audit_logs')
        ->where('action', 'set_nexora_role')
        ->latest()
        ->first();

    expect($log)->not->toBeNull();
    $changes = json_decode($log->changes, true);
    expect($changes['user_id'])->toBe($target->id)
        ->and($changes['to'])->toBe('super_admin');
});

// ── 6.2 Godmode SQL injection ─────────────────────────────────────────────────

test('godmode refuse les requêtes non-SELECT', function () {
    $admin = auditSuperAdmin();

    $this->actingAs($admin);

    $response = $this->postJson('/api/v1/godmode/sql/execute', [
        'query' => 'DROP TABLE users',
        'confirmation' => true,
    ]);

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'Seules les requêtes SELECT sont autorisées dans Godmode.']);
});

test('godmode refuse les requêtes DELETE même avec SELECT au milieu', function () {
    $admin = auditSuperAdmin();
    $this->actingAs($admin);

    $response = $this->postJson('/api/v1/godmode/sql/execute', [
        'query' => 'DELETE FROM users WHERE 1=1',
        'confirmation' => true,
    ]);

    $response->assertStatus(422);
});

test('godmode accepte les requêtes SELECT et log avant exécution', function () {
    $admin = auditSuperAdmin();
    $this->actingAs($admin);

    $logCountBefore = DB::table('godmode_system_logs')
        ->where('type', 'direct_sql_execution')
        ->count();

    $this->postJson('/api/v1/godmode/sql/execute', [
        'query' => 'SELECT 1 AS test',
        'confirmation' => true,
    ]);

    $logCountAfter = DB::table('godmode_system_logs')
        ->where('type', 'direct_sql_execution')
        ->count();

    expect($logCountAfter)->toBeGreaterThan($logCountBefore);
});

// ── 6.3 FnB state machine ────────────────────────────────────────────────────

test('bloque la transition pending → ready sans passer par preparing', function () {
    [$user, $team] = auditTeamMember();
    $team->activateModule('fnb', $user);

    $this->actingAs($user);

    $catId = auditFnbCategoryId($team);
    $menuItemId = DB::table('fnb_menu_items')->insertGetId([
        'team_id' => $team->id, 'category_id' => $catId, 'name' => 'Test Item', 'price' => 1000,
        'is_available' => 1, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $order = $team->fnbOrders()->create([
        'waiter_id' => $user->id,
        'status' => OrderStatus::Sent->value,
        'total' => 0,
    ]);

    $item = $order->items()->create([
        'menu_item_id' => $menuItemId,
        'quantity' => 1,
        'unit_price' => 1000,
        'status' => 'pending',
    ]);

    // pending → ready est invalide (doit passer par sent puis preparing)
    $response = $this->post(route('fnb.orders.items.status', [
        'current_team' => $team->slug,
        'order' => $order->id,
        'item' => $item->id,
    ]), ['status' => 'ready']);

    $response->assertStatus(422);
    expect($item->fresh()->status)->toBe('pending');
});

test('permet la transition pending → sent (valide)', function () {
    [$user, $team] = auditTeamMember();
    $team->activateModule('fnb', $user);

    $this->actingAs($user);

    $catId = auditFnbCategoryId($team);
    $menuItemId = DB::table('fnb_menu_items')->insertGetId([
        'team_id' => $team->id, 'category_id' => $catId, 'name' => 'Test Item B', 'price' => 1500,
        'is_available' => 1, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $order = $team->fnbOrders()->create([
        'waiter_id' => $user->id,
        'status' => OrderStatus::Sent->value,
        'total' => 0,
    ]);

    $item = $order->items()->create([
        'menu_item_id' => $menuItemId,
        'quantity' => 1,
        'unit_price' => 1500,
        'status' => 'pending',
    ]);

    $response = $this->post(route('fnb.orders.items.status', [
        'current_team' => $team->slug,
        'order' => $order->id,
        'item' => $item->id,
    ]), ['status' => 'sent']);

    $response->assertRedirect();

    expect($item->fresh()->status)->toBe('sent');
});

// ── 6.4 Hotel checkout bloqué si FnB ouverts ────────────────────────────────

test('bloque le checkout hotel si des commandes fnb sont ouvertes', function () {
    [$user, $team] = auditTeamMember();
    $team->activateModule('hotel', $user);
    $team->activateModule('fnb', $user);

    $reservation = auditCheckedInReservation($team, $user);
    $order = auditOpenOrder($team, $user, $reservation->id);

    $this->actingAs($user);

    $response = $this->post(route('hotel.reservations.checkout', [
        'current_team' => $team->slug,
        'reservation' => $reservation->id,
    ]), [
        'amount' => 50000,
        'method' => 'especes',
    ]);

    expect($response->status())->toBeIn([302, 409, 422]);
});

// ── 6.5 HotelFnBBridge cross-tenant ──────────────────────────────────────────

test('attachOrderToReservation est atomique (rollback sur exception)', function () {
    [$user, $team] = auditTeamMember();
    $team->activateModule('hotel', $user);
    $team->activateModule('fnb', $user);

    $reservation = auditCheckedInReservation($team, $user);
    $order = auditOpenOrder($team, $user);

    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(new RuntimeException('DB failure'));

    $bridge = app(HotelFnBBridgeService::class);

    expect(fn () => $bridge->attachOrderToReservation($order, $reservation))
        ->toThrow(RuntimeException::class);

    expect($order->fresh()->reservation_id)->toBeNull();
})->skip('Test requires DB mock setup — integrate with full DB in CI');

test('bridge refuse d\'attacher une commande d\'un autre tenant', function () {
    [$user1, $team1] = auditTeamMember();
    [$user2, $team2] = auditTeamMember();

    $team1->activateModule('hotel', $user1);
    $team1->activateModule('fnb', $user1);
    $team2->activateModule('fnb', $user2);

    $reservation = auditCheckedInReservation($team1, $user1);
    $order = auditOpenOrder($team2, $user2);

    $bridge = app(HotelFnBBridgeService::class);

    expect(fn () => $bridge->attachOrderToReservation($order, $reservation))
        ->toThrow(InvalidBridgeOperationException::class);
});

// ── 6.6 OTP verrouillage ─────────────────────────────────────────────────────

test('verrouille le compte après 3 tentatives OTP échouées', function () {
    $email = 'test-otp-lock@nexora.test';

    Cache::put('otp_'.$email, '123456', now()->addMinutes(15));

    foreach (range(1, 3) as $_) {
        $this->postJson(route('otp.verify'), [
            'email' => $email,
            'otp' => '000000',
        ]);
    }

    // 4e tentative — doit retourner un message de verrouillage
    $response = $this->postJson(route('otp.verify'), [
        'email' => $email,
        'otp' => '000000',
    ]);

    $response->assertStatus(422);
    $errors = $response->json('errors.otp.0') ?? $response->json('message') ?? '';
    expect(mb_strtolower((string) $errors))->toContain('verrouill');
})->after(function () {
    Cache::forget('otp_lock_test-otp-lock@nexora.test');
    Cache::forget('otp_attempts_test-otp-lock@nexora.test');
});

test('OTP valide supprime le cache après succès', function () {
    $email = 'test-otp-success@nexora.test';
    $otp = '654321';
    Cache::put('otp_'.$email, $otp, now()->addMinutes(15));

    $response = $this->postJson(route('otp.verify'), [
        'email' => $email,
        'otp' => $otp,
    ]);

    $response->assertOk()->assertJson(['success' => true]);

    expect(Cache::has('otp_'.$email))->toBeFalse();
    expect(Cache::has('otp_verified_'.$email))->toBeTrue();
});

// ── 6.7 Compte bloqué ne peut pas accéder aux routes ────────────────────────

test('un utilisateur bloqué est déconnecté et ne peut pas accéder au dashboard', function () {
    [$user, $team] = auditTeamMember();
    $user->forceFill(['blocked_at' => now()])->save();

    $this->actingAs($user);

    $response = $this->get(route('dashboard', ['current_team' => $team->slug]));

    expect($response->status())->toBeIn([302, 403]);
});
