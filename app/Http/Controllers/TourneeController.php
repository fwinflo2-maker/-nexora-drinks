<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTourneeRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Client;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\DeliveryRoute;
use App\Models\PackagingMovement;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TourneeController extends Controller
{
    /**
     * Liste des tournées paginées avec chauffeur et nombre de livraisons.
     */
    public function index(Request $request, Team $current_team): Response
    {
        $routes = DeliveryRoute::where('team_id', $current_team->id)
            ->with(['driver:id,name', 'deliveries:id,delivery_route_id,status'])
            ->latest()
            ->paginate(20)
            ->through(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'date' => $r->date,
                'status' => $r->status,
                'driver_name' => $r->driver?->name,
                'deliveries_count' => $r->deliveries->count(),
                'completed_count' => $r->deliveries->where('status', 'delivered')->count(),
            ]);

        $clients = Client::where('team_id', $current_team->id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        $stats = [
            'total' => DeliveryRoute::where('team_id', $current_team->id)->count(),
            'planned' => DeliveryRoute::where('team_id', $current_team->id)->where('status', 'planned')->count(),
            'in_progress' => DeliveryRoute::where('team_id', $current_team->id)->where('status', 'in_progress')->count(),
            'completed' => DeliveryRoute::where('team_id', $current_team->id)->where('status', 'completed')->count(),
        ];

        return Inertia::render('tournees/index', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'tournees' => $routes,
            'clients' => $clients,
            'stats' => $stats,
        ]);
    }

    /**
     * Créer une nouvelle tournée.
     */
    public function store(StoreTourneeRequest $request, Team $current_team): RedirectResponse
    {
        $validated = $request->validated();

        DeliveryRoute::create([
            'team_id' => $current_team->id,
            'name' => $validated['name'],
            'date' => $validated['date'],
            'driver_id' => $validated['driver_id'] ?? null,
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'status' => 'planned',
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Tournée créée.');
    }

    /**
     * Détail d'une tournée avec ses livraisons (feuille de route livreur).
     */
    public function show(Request $request, Team $current_team, DeliveryRoute $deliveryRoute): Response
    {
        abort_if($deliveryRoute->team_id !== $current_team->id, 403);

        $deliveryRoute->load([
            'driver:id,name',
            'vehicle:id,name,plate',
            'deliveries' => fn ($q) => $q->orderBy('sequence_number'),
            'deliveries.client:id,name,address,phone,phone2,zone,gps_lat,gps_lng,client_type',
            'deliveries.order.items.product:id,name,sku',
        ]);

        $deliveries = $deliveryRoute->deliveries->map(fn (Delivery $delivery) => [
            'id' => $delivery->id,
            'sequence_number' => $delivery->sequence_number,
            'status' => $delivery->status,
            'delivered_at' => $delivery->delivered_at,
            'notes' => $delivery->notes,
            'client' => $delivery->client ? [
                'id' => $delivery->client->id,
                'name' => $delivery->client->name,
                'address' => $delivery->client->address,
                'phone' => $delivery->client->phone,
                'phone2' => $delivery->client->phone2,
                'zone' => $delivery->client->zone,
                'gps_lat' => $delivery->client->gps_lat,
                'gps_lng' => $delivery->client->gps_lng,
                'client_type' => $delivery->client->client_type,
            ] : null,
            'order' => $delivery->order ? [
                'id' => $delivery->order->id,
                'order_number' => $delivery->order->order_number,
                'status' => $delivery->order->status,
                'delivery_date' => $delivery->order->delivery_date,
                'subtotal' => $delivery->order->subtotal,
                'discount_amount' => $delivery->order->discount_amount,
                'total' => $delivery->order->total,
                'notes' => $delivery->order->notes,
                'items' => $delivery->order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product?->name,
                    'product_sku' => $item->product?->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount_pct' => $item->discount_pct,
                    'line_total' => $item->line_total,
                ])->values(),
            ] : null,
        ])->values();

        $statusCounts = $deliveryRoute->deliveries->countBy('status');

        $stats = [
            'total' => $deliveryRoute->deliveries->count(),
            'delivered' => $statusCounts->get('delivered', 0),
            'pending' => $statusCounts->get('pending', 0),
            'failed' => $statusCounts->get('failed', 0) + $statusCounts->get('partial', 0),
        ];

        return Inertia::render('tournees/show', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'route' => [
                'id' => $deliveryRoute->id,
                'name' => $deliveryRoute->name,
                'date' => $deliveryRoute->date,
                'status' => $deliveryRoute->status,
                'departure_time' => $deliveryRoute->departure_time,
                'arrival_time' => $deliveryRoute->arrival_time,
                'total_distance_km' => $deliveryRoute->total_distance_km,
                'driver' => $deliveryRoute->driver ? $deliveryRoute->driver->only('id', 'name') : null,
                'vehicle' => $deliveryRoute->vehicle ? $deliveryRoute->vehicle->only('id', 'name', 'plate') : null,
            ],
            'deliveries' => $deliveries,
            'stats' => $stats,
        ]);
    }

    /**
     * Mettre à jour le statut d'une tournée.
     */
    public function update(Request $request, Team $current_team, DeliveryRoute $deliveryRoute): RedirectResponse
    {
        abort_if($deliveryRoute->team_id !== $current_team->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:planned,in_progress,completed,cancelled'],
        ]);

        $deliveryRoute->update(['status' => $validated['status']]);

        return back()->with('success', 'Tournée mise à jour.');
    }

    /**
     * Supprimer une tournée (uniquement si statut = planned).
     */
    public function destroy(Request $request, Team $current_team, DeliveryRoute $deliveryRoute): RedirectResponse
    {
        abort_if($deliveryRoute->team_id !== $current_team->id, 403);

        if ($deliveryRoute->status !== 'planned') {
            return back()->with('error', 'Seules les tournées planifiées peuvent être supprimées.');
        }

        $deliveryRoute->delete();

        return back()->with('success', 'Tournée supprimée.');
    }

    /**
     * Détail d'une livraison dans une tournée.
     */
    public function showDelivery(Request $request, Team $current_team, DeliveryRoute $deliveryRoute, Delivery $delivery): Response
    {
        abort_if($deliveryRoute->team_id !== $current_team->id, 403);
        abort_if($delivery->route_id !== $deliveryRoute->id, 403);

        $delivery->load([
            'client:id,name,phone,phone2,address,gps_lat,gps_lng,zone,client_type',
            'order.items.product:id,name,sku',
        ]);

        // Navigation en une seule requête
        $sequenceIds = $deliveryRoute->deliveries()
            ->orderBy('sequence_number')
            ->pluck('id', 'sequence_number');

        $allSeqs = $sequenceIds->keys()->sort()->values();
        $currentIndex = $allSeqs->search($delivery->sequence_number);

        $prevId = $currentIndex > 0
            ? $sequenceIds->get($allSeqs->get($currentIndex - 1))
            : null;
        $nextId = $currentIndex < $allSeqs->count() - 1
            ? $sequenceIds->get($allSeqs->get($currentIndex + 1))
            : null;

        return Inertia::render('tournees/delivery', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'route' => $deliveryRoute->only('id', 'name', 'date', 'status'),
            'delivery' => [
                'id' => $delivery->id,
                'sequence_number' => $delivery->sequence_number,
                'status' => $delivery->status,
                'delivered_at' => $delivery->delivered_at,
                'notes' => $delivery->notes,
                'client' => $delivery->client ? [
                    'id' => $delivery->client->id,
                    'name' => $delivery->client->name,
                    'phone' => $delivery->client->phone,
                    'phone2' => $delivery->client->phone2,
                    'address' => $delivery->client->address,
                    'gps_lat' => $delivery->client->gps_lat,
                    'gps_lng' => $delivery->client->gps_lng,
                    'zone' => $delivery->client->zone,
                    'client_type' => $delivery->client->client_type instanceof \BackedEnum
                        ? $delivery->client->client_type->value
                        : $delivery->client->client_type,
                ] : null,
                'order' => $delivery->order ? [
                    'id' => $delivery->order->id,
                    'order_number' => $delivery->order->order_number,
                    'subtotal' => $delivery->order->subtotal,
                    'discount_amount' => $delivery->order->discount_amount,
                    'total' => $delivery->order->total,
                    'notes' => $delivery->order->notes,
                    'items' => $delivery->order->items->map(fn ($item) => [
                        'id' => $item->id,
                        'product_name' => $item->product?->name,
                        'product_sku' => $item->product?->sku,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'discount_pct' => $item->discount_pct,
                        'line_total' => $item->line_total,
                    ])->values(),
                ] : null,
            ],
            'navigation' => [
                'prev_id' => $prevId,
                'next_id' => $nextId,
                'total' => $allSeqs->count(),
                'current_position' => $currentIndex + 1,
            ],
        ]);
    }

    /**
     * Mettre à jour le statut et les notes d'une livraison.
     */
    public function updateDelivery(UpdateDeliveryRequest $request, Team $current_team, DeliveryRoute $deliveryRoute, Delivery $delivery): RedirectResponse
    {
        abort_if($deliveryRoute->team_id !== $current_team->id, 403);
        abort_if($delivery->route_id !== $deliveryRoute->id, 403);

        $validated = $request->validated();

        $updateData = [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $delivery->notes,
        ];

        if ($validated['status'] === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        $delivery->update($updateData);

        if ($validated['status'] === 'partial' && ! empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                DeliveryItem::where('delivery_id', $delivery->id)
                    ->where('id', $itemData['id'])
                    ->update(['delivered_qty' => $itemData['delivered_qty']]);
            }
        }

        return back()->with('success', 'Livraison mise à jour.');
    }

    /**
     * Enregistrer une collecte (cash + retours d'emballages consignés) sur une tournée.
     */
    public function storeCollection(Request $request, Team $current_team, DeliveryRoute $deliveryRoute): RedirectResponse
    {
        abort_if($deliveryRoute->team_id !== $current_team->id, 403);

        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'packaging_returns' => ['nullable', 'array'],
            'packaging_returns.*.packaging_type_id' => ['required', 'integer', 'exists:packaging_types,id'],
            'packaging_returns.*.quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! empty($validated['packaging_returns'])) {
            foreach ($validated['packaging_returns'] as $return) {
                PackagingMovement::create([
                    'team_id' => $current_team->id,
                    'client_id' => $validated['client_id'],
                    'packaging_type_id' => $return['packaging_type_id'],
                    'movement_type' => 'in',
                    'quantity' => $return['quantity'],
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => $request->user()->id,
                ]);
            }
        }

        return back()->with('success', 'Collecte enregistrée.');
    }
}
