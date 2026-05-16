<?php

use App\Models\Client;
use App\Models\Delivery;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── index() ───────────────────────────────────────────────────────────────────

test('tournees index retourne les tournées paginées avec chauffeur et nb livraisons', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    DeliveryRoute::factory()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('tournees.index', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tournees/index')
            ->has('tournees')
        );
});

test('tournees index refuse un non-membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get(route('tournees.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('tournees index isole les données par team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $otherUser = User::factory()->create();
    DeliveryRoute::factory()->create([
        'team_id' => $otherUser->currentTeam->id,
        'created_by' => $otherUser->id,
    ]);

    $this->actingAs($user)
        ->get(route('tournees.index', ['current_team' => $team->slug]))
        ->assertOk();

    expect(DeliveryRoute::where('team_id', $team->id)->count())->toBe(0);
});

// ── store() ───────────────────────────────────────────────────────────────────

test('store crée une tournée pour la team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('tournees.store', ['current_team' => $team->slug]), [
            'name' => 'Tournée Akwa - Test',
            'date' => now()->addDay()->toDateString(),
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('routes', [
        'team_id' => $team->id,
        'name' => 'Tournée Akwa - Test',
    ]);
});

test('store valide les champs requis', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->post(route('tournees.store', ['current_team' => $team->slug]), [])
        ->assertSessionHasErrors(['name', 'date']);
});

// ── show() ────────────────────────────────────────────────────────────────────

test('show retourne la tournée avec ses livraisons', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $route = DeliveryRoute::factory()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
    ]);
    Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
        'status' => 'delivered',
        'sequence_number' => 1,
    ]);
    Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
        'status' => 'pending',
        'sequence_number' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('tournees.show', ['current_team' => $team->slug, 'deliveryRoute' => $route->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tournees/show')
            ->has('route', fn ($prop) => $prop
                ->hasAll(['id', 'name', 'date', 'status', 'departure_time', 'arrival_time', 'total_distance_km', 'driver', 'vehicle'])
            )
            ->has('deliveries', 2, fn ($prop) => $prop
                ->hasAll(['id', 'sequence_number', 'status', 'delivered_at', 'notes', 'client', 'order'])
                ->has('client', fn ($client) => $client
                    ->hasAll(['id', 'name', 'address', 'phone', 'phone2', 'zone', 'gps_lat', 'gps_lng', 'client_type'])
                )
                ->has('order', fn ($order) => $order
                    ->hasAll(['id', 'order_number', 'status', 'delivery_date', 'subtotal', 'discount_amount', 'total', 'notes', 'items'])
                )
            )
            ->has('stats', fn ($prop) => $prop
                ->where('total', 2)
                ->where('delivered', 1)
                ->where('pending', 1)
                ->where('failed', 0)
            )
        );
});

test('show refuse un accès cross-team', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam;

    $route = DeliveryRoute::factory()->create([
        'team_id' => $otherTeam->id,
        'created_by' => $otherUser->id,
    ]);

    // BelongsToTeam global scope returns 404 (not found) for cross-team resources
    $this->actingAs($user)
        ->get(route('tournees.show', ['current_team' => $user->currentTeam->slug, 'deliveryRoute' => $route->id]))
        ->assertNotFound();
});

test('show trie les livraisons par sequence_number', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $route = DeliveryRoute::factory()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
    ]);

    Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
        'sequence_number' => 3,
    ]);
    Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
        'sequence_number' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('tournees.show', ['current_team' => $team->slug, 'deliveryRoute' => $route->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tournees/show')
            ->where('deliveries.0.sequence_number', 1)
            ->where('deliveries.1.sequence_number', 3)
        );
});

// ── update() ──────────────────────────────────────────────────────────────────

test('update modifie le statut d\'une tournée', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $route = DeliveryRoute::factory()->planned()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->patch(route('tournees.update', ['current_team' => $team->slug, 'deliveryRoute' => $route->id]), [
            'status' => 'in_progress',
        ])
        ->assertRedirect();

    expect($route->fresh()->status)->toBe('in_progress');
});

// ── destroy() ─────────────────────────────────────────────────────────────────

test('destroy supprime une tournée planned', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $route = DeliveryRoute::factory()->planned()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('tournees.destroy', ['current_team' => $team->slug, 'deliveryRoute' => $route->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('routes', ['id' => $route->id]);
});

test('destroy refuse de supprimer une tournée completed', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $route = DeliveryRoute::factory()->completed()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('tournees.destroy', ['current_team' => $team->slug, 'deliveryRoute' => $route->id]))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('routes', ['id' => $route->id]);
});

// ── showDelivery() ────────────────────────────────────────────────────────────

test('showDelivery retourne le détail d\'une livraison', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $route = DeliveryRoute::factory()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
    ]);
    $delivery = Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
    ]);

    $this->actingAs($user)
        ->get(route('tournees.deliveries.show', [
            'current_team' => $team->slug,
            'deliveryRoute' => $route->id,
            'delivery' => $delivery->id,
        ]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tournees/delivery')
            ->has('delivery')
            ->has('route')
        );
});

// ── updateDelivery() ──────────────────────────────────────────────────────────

test('updateDelivery met à jour le statut d\'une livraison', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $route = DeliveryRoute::factory()->create([
        'team_id' => $team->id,
        'created_by' => $user->id,
    ]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'created_by' => $user->id,
    ]);
    $delivery = Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->patch(route('tournees.deliveries.update', [
            'current_team' => $team->slug,
            'deliveryRoute' => $route->id,
            'delivery' => $delivery->id,
        ]), [
            'status' => 'delivered',
        ])
        ->assertRedirect();

    expect($delivery->fresh()->status)->toBe('delivered');
    expect($delivery->fresh()->delivered_at)->not->toBeNull();
});

// ── showDelivery() — nouveaux tests ───────────────────────────────────────────

test('showDelivery retourne client, order.items et navigation', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $route = DeliveryRoute::factory()->create(['team_id' => $team->id, 'created_by' => $user->id]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $product = Product::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'created_by' => $user->id]);
    OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);
    $delivery = Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
        'sequence_number' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('tournees.deliveries.show', [
            'current_team' => $team->slug,
            'deliveryRoute' => $route->id,
            'delivery' => $delivery->id,
        ]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tournees/delivery')
            ->has('delivery.client')
            ->has('delivery.order.items', 1)
            ->has('navigation.prev_id')
            ->has('navigation.next_id')
            ->has('navigation.total')
            ->has('navigation.current_position')
        );
});

test('showDelivery navigation: premier stop sans prev, dernier sans next', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $route = DeliveryRoute::factory()->create(['team_id' => $team->id, 'created_by' => $user->id]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'created_by' => $user->id]);

    $d1 = Delivery::factory()->create(['team_id' => $team->id, 'route_id' => $route->id, 'client_id' => $client->id, 'order_id' => $order->id, 'sequence_number' => 1]);
    $d2 = Delivery::factory()->create(['team_id' => $team->id, 'route_id' => $route->id, 'client_id' => $client->id, 'order_id' => $order->id, 'sequence_number' => 2]);
    $d3 = Delivery::factory()->create(['team_id' => $team->id, 'route_id' => $route->id, 'client_id' => $client->id, 'order_id' => $order->id, 'sequence_number' => 3]);

    $this->actingAs($user)
        ->get(route('tournees.deliveries.show', ['current_team' => $team->slug, 'deliveryRoute' => $route->id, 'delivery' => $d1->id]))
        ->assertInertia(fn ($page) => $page
            ->where('navigation.prev_id', null)
            ->where('navigation.next_id', $d2->id)
            ->where('navigation.current_position', 1)
            ->where('navigation.total', 3)
        );

    $this->actingAs($user)
        ->get(route('tournees.deliveries.show', ['current_team' => $team->slug, 'deliveryRoute' => $route->id, 'delivery' => $d3->id]))
        ->assertInertia(fn ($page) => $page
            ->where('navigation.prev_id', $d2->id)
            ->where('navigation.next_id', null)
            ->where('navigation.current_position', 3)
        );
});

test('showDelivery refuse une livraison appartenant à une autre route', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $route1 = DeliveryRoute::factory()->create(['team_id' => $team->id, 'created_by' => $user->id]);
    $route2 = DeliveryRoute::factory()->create(['team_id' => $team->id, 'created_by' => $user->id]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'created_by' => $user->id]);
    $delivery = Delivery::factory()->create(['team_id' => $team->id, 'route_id' => $route2->id, 'client_id' => $client->id, 'order_id' => $order->id]);

    $this->actingAs($user)
        ->get(route('tournees.deliveries.show', [
            'current_team' => $team->slug,
            'deliveryRoute' => $route1->id,
            'delivery' => $delivery->id,
        ]))
        ->assertForbidden();
});

// ── updateDelivery() — nouveaux tests ─────────────────────────────────────────

test('updateDelivery accepte le statut partial avec les qtés livrées', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $route = DeliveryRoute::factory()->create(['team_id' => $team->id, 'created_by' => $user->id]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $product = Product::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'created_by' => $user->id]);
    $orderItem = OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);
    $delivery = Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->patch(route('tournees.deliveries.update', [
            'current_team' => $team->slug,
            'deliveryRoute' => $route->id,
            'delivery' => $delivery->id,
        ]), [
            'status' => 'partial',
            'notes' => 'Manque 2 bouteilles',
            'items' => [['id' => $orderItem->id, 'delivered_qty' => 8]],
        ])
        ->assertRedirect();

    expect($delivery->fresh()->status)->toBe('partial');
    expect($delivery->fresh()->notes)->toBe('Manque 2 bouteilles');
});

test('updateDelivery rejette un statut invalide', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $route = DeliveryRoute::factory()->create(['team_id' => $team->id, 'created_by' => $user->id]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $order = Order::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'created_by' => $user->id]);
    $delivery = Delivery::factory()->create([
        'team_id' => $team->id,
        'route_id' => $route->id,
        'client_id' => $client->id,
        'order_id' => $order->id,
    ]);

    $this->actingAs($user)
        ->patch(route('tournees.deliveries.update', [
            'current_team' => $team->slug,
            'deliveryRoute' => $route->id,
            'delivery' => $delivery->id,
        ]), ['status' => 'mauvais_statut'])
        ->assertSessionHasErrors(['status']);
});
