<?php

declare(strict_types=1);

use App\Enums\FnB\OrderStatus;
use App\Enums\TeamRole;
use App\Models\FnB\OrderItem;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('debug: what does the route return for pending → sent', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['is_active' => true]);
    $team->members()->attach($user->id, ['role' => TeamRole::Admin->value]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    $user = $user->fresh();

    $team->activateModule('fnb', $user);

    $this->actingAs($user);

    $catId = DB::table('fnb_categories')->insertGetId([
        'team_id' => $team->id, 'name' => 'Debug Cat',
        'is_active' => 1, 'sort_order' => 1,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $menuItemId = DB::table('fnb_menu_items')->insertGetId([
        'team_id' => $team->id, 'category_id' => $catId,
        'name' => 'Debug Item', 'price' => 1000,
        'is_available' => 1, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $order = $team->fnbOrders()->create([
        'waiter_id' => $user->id,
        'status' => OrderStatus::Open->value,
        'total' => 0,
    ]);

    $itemId = DB::table('fnb_order_items')->insertGetId([
        'order_id' => $order->id,
        'menu_item_id' => $menuItemId,
        'quantity' => 1,
        'unit_price' => 1000,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $item = OrderItem::find($itemId);

    expect($item->status)->toBe('pending');

    $response = $this->post(route('fnb.orders.items.status', [
        'current_team' => $team->slug,
        'order' => $order->id,
        'item' => $item->id,
    ]), ['status' => 'sent']);

    $statusCode = $response->status();
    $content = $response->getContent();
    $itemStatus = $item->fresh()->status;

    // This will show up in test output on failure
    expect($statusCode)->toBeIn([301, 302, 303, 307, 308],
        "Got {$statusCode}. Body: {$content}. Item status after: {$itemStatus}"
    );
});
