<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Http\Controllers\Controller;
use App\Models\FnB\Order;
use App\Models\FnB\OrderItem;
use App\Models\FnB\Table;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $stats = [
            'tables_free' => Table::where('status', 'free')->count(),
            'tables_occupied' => Table::where('status', 'occupied')->count(),
            'orders_open' => Order::active()->count(),
            'revenue_today' => (float) Order::whereDate('closed_at', today())
                ->where('status', 'closed')
                ->sum('total'),
        ];

        $activeOrders = Order::with(['table', 'items.menuItem'])
            ->active()
            ->latest()
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'reference' => $o->reference,
                'table_name' => $o->table?->name,
                'status' => $o->status,
                'total' => $o->total,
                'items_count' => $o->items->count(),
            ]);

        $kitchenOrders = Order::with(['table', 'items.menuItem'])
            ->whereIn('status', ['sent', 'preparing'])
            ->latest()
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'reference' => $o->reference,
                'table_name' => $o->table?->name,
                'status' => $o->status,
                'items' => $o->items->map(fn ($i) => [
                    'id' => $i->id,
                    'name' => $i->menuItem?->name,
                    'quantity' => $i->quantity,
                    'status' => $i->status,
                    'notes' => $i->notes,
                ]),
            ]);

        $topItems = OrderItem::with('menuItem')
            ->whereHas('order', fn ($q) => $q->whereDate('created_at', today()))
            ->selectRaw('menu_item_id, SUM(quantity) as total_qty')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'name' => $i->menuItem?->name,
                'total_qty' => (int) $i->total_qty,
            ]);

        $revenueByHour = collect(range(8, 22))->map(function (int $hour) {
            return [
                'hour' => "{$hour}h",
                'revenue' => (float) Order::whereDate('closed_at', today())
                    ->whereTime('closed_at', '>=', "{$hour}:00")
                    ->whereTime('closed_at', '<', ($hour + 1).':00')
                    ->sum('total'),
            ];
        })->values();

        return Inertia::render('fnb/Dashboard', compact(
            'stats',
            'activeOrders',
            'kitchenOrders',
            'topItems',
            'revenueByHour',
        ));
    }

    public function kitchen(): Response
    {
        $orders = Order::with(['table', 'items.menuItem'])
            ->whereIn('status', ['sent', 'preparing'])
            ->latest()
            ->get();

        return Inertia::render('fnb/Kitchen', ['orders' => $orders]);
    }
}
