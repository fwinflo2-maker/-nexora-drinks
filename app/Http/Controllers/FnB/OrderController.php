<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Domain\HotelFnB\Services\HotelFnBBridgeService;
use App\Enums\FnB\OrderStatus;
use App\Events\FnB\OrderClosed;
use App\Exceptions\Hotel\InvalidBridgeOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FnB\AddOrderItemRequest;
use App\Http\Requests\FnB\StoreOrderRequest;
use App\Http\Requests\FnB\UpdateOrderItemStatusRequest;
use App\Models\FnB\Order;
use App\Models\FnB\OrderItem;
use App\Models\FnB\Table;
use App\Models\Hotel\Reservation;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    private const VALID_ITEM_TRANSITIONS = [
        'pending' => ['sent'],
        'sent' => ['preparing'],
        'preparing' => ['ready'],
        'ready' => ['served'],
    ];

    public function __construct(private readonly HotelFnBBridgeService $bridge) {}

    public function index(Request $request, Team $current_team): Response
    {
        $status = $request->get('status');

        $orders = $current_team->fnbOrders()
            ->with(['table', 'waiter', 'items.menuItem'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('fnb/orders/index', [
            'orders' => $orders,
            'filters' => ['status' => $status ?? ''],
            'statuses' => array_map(fn ($s) => ['value' => $s->value, 'name' => $s->label()], OrderStatus::cases()),
        ]);
    }

    public function create(Team $current_team): Response
    {
        return Inertia::render('fnb/orders/create', [
            'tables' => $current_team->fnbTables()->where('status', 'free')->orderBy('name')->get(),
            'categories' => $current_team->fnbCategories()
                ->with(['menuItems' => fn ($q) => $q->where('is_available', true)->orderBy('name')])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function store(StoreOrderRequest $request, Team $current_team): RedirectResponse
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data, $current_team, $request) {
            $order = $current_team->fnbOrders()->create([
                'table_id' => $data['table_id'],
                'waiter_id' => $request->user()->id,
                'status' => OrderStatus::Open->value,
                'notes' => $data['notes'] ?? null,
                'total' => 0,
            ]);

            $total = 0.0;
            foreach ($data['items'] as $itemData) {
                $menuItem = $current_team->fnbMenuItems()->findOrFail($itemData['menu_item_id']);
                $unitPrice = (float) $menuItem->price;
                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'status' => 'pending',
                    'notes' => $itemData['notes'] ?? null,
                ]);
                $total += $unitPrice * $itemData['quantity'];
            }

            $order->update(['total' => $total]);

            Table::withoutGlobalScopes()->where('id', $data['table_id'])->update(['status' => 'occupied']);

            return $order;
        });

        return to_route('fnb.orders.show', [$current_team->slug, $order])
            ->with('toast', ['type' => 'success', 'message' => "Commande {$order->reference} créée."]);
    }

    public function show(Team $current_team, Order $order): Response
    {
        $order->load(['table', 'waiter', 'items.menuItem', 'validator']);

        return Inertia::render('fnb/orders/show', [
            'order' => $order,
        ]);
    }

    public function addItem(AddOrderItemRequest $request, Team $current_team, Order $order): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::Open, 422, 'Commande non modifiable.');

        $data = $request->validated();

        $menuItem = $current_team->fnbMenuItems()->findOrFail($data['menu_item_id']);

        DB::transaction(function () use ($order, $menuItem, $data) {
            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'quantity' => $data['quantity'],
                'unit_price' => $menuItem->price,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $order->update([
                'total' => $order->items()->get()->sum(fn ($i) => (float) $i->unit_price * $i->quantity),
            ]);
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Article ajouté.']);
    }

    public function removeItem(Team $current_team, Order $order, OrderItem $item): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::Open, 422, 'Commande non modifiable.');

        DB::transaction(function () use ($order, $item) {
            $item->delete();

            $order->update([
                'total' => $order->items()->get()->sum(fn ($i) => (float) $i->unit_price * $i->quantity),
            ]);
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Article retiré.']);
    }

    public function sendToKitchen(Team $current_team, Order $order): RedirectResponse
    {
        $this->authorize('sendToKitchen', $order);

        abort_unless($order->status === OrderStatus::Open, 422, 'Commande déjà envoyée.');
        abort_unless($order->items()->exists(), 422, 'Commande vide.');

        $order->update(['status' => OrderStatus::Sent->value]);
        $order->items()->where('status', 'pending')->update(['status' => 'sent']);

        return back()->with('toast', ['type' => 'success', 'message' => 'Commande envoyée en cuisine.']);
    }

    public function updateItemStatus(UpdateOrderItemStatusRequest $request, Team $current_team, Order $order, OrderItem $item): RedirectResponse
    {
        $data = $request->validated();
        $newStatus = $data['status'];
        $currentStatus = $item->status;

        $validNext = self::VALID_ITEM_TRANSITIONS[$currentStatus] ?? [];
        abort_unless(
            in_array($newStatus, $validNext, true),
            422,
            "Transition invalide : {$currentStatus} → {$newStatus}."
        );

        $item->update(['status' => $newStatus]);

        if ($data['status'] === 'ready' && $order->items()->where('status', '!=', 'ready')->doesntExist()) {
            $order->update(['status' => OrderStatus::Ready->value]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Statut mis à jour.']);
    }

    public function close(Request $request, Team $current_team, Order $order): RedirectResponse
    {
        $this->authorize('close', $order);

        abort_unless(
            in_array($order->status->value, [OrderStatus::Open->value, OrderStatus::Ready->value, OrderStatus::Sent->value, OrderStatus::Preparing->value], true),
            422,
            'Commande déjà clôturée ou annulée.'
        );

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'status' => OrderStatus::Closed->value,
                'closed_at' => now(),
                'validated_by' => $request->user()->id,
                'validated_at' => now(),
            ]);

            Table::withoutGlobalScopes()->where('id', $order->table_id)->update(['status' => 'free']);
        });

        OrderClosed::dispatch($order->fresh());

        return back()->with('toast', ['type' => 'success', 'message' => 'Commande clôturée.']);
    }

    public function attachToReservation(Request $request, Team $current_team, Order $order): RedirectResponse
    {
        $request->validate(['reservation_id' => ['required', 'integer']]);

        $reservation = Reservation::withoutGlobalScopes()
            ->where('team_id', $current_team->id)
            ->findOrFail($request->integer('reservation_id'));

        try {
            $this->bridge->attachOrderToReservation($order, $reservation);
        } catch (InvalidBridgeOperationException $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => "Commande rattachée à la réservation {$reservation->reference}."]);
    }

    public function detachFromReservation(Team $current_team, Order $order): RedirectResponse
    {
        try {
            $this->bridge->detachOrderFromReservation($order);
        } catch (InvalidBridgeOperationException $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Commande détachée de la réservation.']);
    }

    public function cancel(Team $current_team, Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        abort_unless(
            in_array($order->status->value, [OrderStatus::Open->value, OrderStatus::Sent->value], true),
            422,
            'Impossible d\'annuler cette commande.'
        );

        DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::Cancelled->value]);

            Table::withoutGlobalScopes()->where('id', $order->table_id)->update(['status' => 'free']);
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Commande annulée.']);
    }
}
