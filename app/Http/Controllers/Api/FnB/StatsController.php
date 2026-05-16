<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\FnB;

use App\Http\Controllers\Controller;
use App\Models\FnB\Order;
use App\Models\FnB\OrderItem;
use App\Models\FnB\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $topItems = OrderItem::with('menuItem')
            ->whereHas('order', fn ($q) => $q->whereDate('created_at', today()))
            ->selectRaw('menu_item_id, SUM(quantity) as total_qty')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'name' => $i->menuItem?->name,
                'quantity' => (int) $i->total_qty,
            ]);

        return response()->json([
            'orders_open' => Order::active()->count(),
            'tables_free' => Table::where('status', 'free')->count(),
            'revenue_today' => (float) Order::whereDate('closed_at', today())
                ->where('status', 'closed')
                ->sum('total'),
            'top_items' => $topItems,
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $orders = Order::with(['table', 'items.menuItem', 'waiter'])
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }
}
