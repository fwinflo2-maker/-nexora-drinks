<?php

declare(strict_types=1);

namespace App\Services\FnB;

use App\Enums\FnB\OrderStatus;
use App\Models\FnB\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /** @return array<int, array<string, mixed>> */
    public function ordersReport(int $teamId, string $dateFrom, string $dateTo): array
    {
        return Order::with(['table', 'waiter'])
            ->where('team_id', $teamId)
            ->where('status', OrderStatus::Closed->value)
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->orderBy('created_at')
            ->get()
            ->map(fn (Order $o) => [
                'reference' => $o->reference,
                'table_name' => $o->table?->name ?? '—',
                'waiter_name' => $o->waiter?->name ?? '—',
                'items_count' => $o->items()->count(),
                'total' => (float) $o->total,
                'closed_at' => $o->closed_at?->format('d/m/Y H:i') ?? '—',
            ])
            ->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    public function revenueReport(int $teamId, string $dateFrom, string $dateTo): array
    {
        return Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as revenue'),
        )
            ->where('team_id', $teamId)
            ->where('status', OrderStatus::Closed->value)
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => Carbon::parse($row->date)->format('d/m/Y'),
                'count' => (int) $row->count,
                'revenue' => (float) $row->revenue,
            ])
            ->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    public function menuReport(int $teamId, string $dateFrom, string $dateTo): array
    {
        return DB::table('fnb_order_items')
            ->join('fnb_orders', 'fnb_order_items.order_id', '=', 'fnb_orders.id')
            ->join('fnb_menu_items', 'fnb_order_items.menu_item_id', '=', 'fnb_menu_items.id')
            ->leftJoin('fnb_categories', 'fnb_menu_items.category_id', '=', 'fnb_categories.id')
            ->where('fnb_orders.team_id', $teamId)
            ->where('fnb_orders.status', OrderStatus::Closed->value)
            ->whereBetween(DB::raw('DATE(fnb_orders.created_at)'), [$dateFrom, $dateTo])
            ->select(
                'fnb_menu_items.name as item_name',
                'fnb_categories.name as category_name',
                DB::raw('SUM(fnb_order_items.quantity) as total_qty'),
                DB::raw('SUM(fnb_order_items.quantity * fnb_order_items.unit_price) as total_revenue'),
            )
            ->groupBy('fnb_menu_items.id', 'fnb_menu_items.name', 'fnb_categories.name')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(fn ($row) => [
                'item_name' => $row->item_name,
                'category_name' => $row->category_name ?? '—',
                'total_qty' => (int) $row->total_qty,
                'total_revenue' => (float) $row->total_revenue,
            ])
            ->toArray();
    }
}
