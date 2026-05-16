<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\TeamRole;
use App\Http\Controllers\Controller;
use App\Models\Drinks\ActivityLog;
use App\Models\Drinks\Article;
use App\Models\Drinks\CashDeposit;
use App\Models\Drinks\CashInput;
use App\Models\Drinks\Expense;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\Loss;
use App\Models\Drinks\Payment;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\Sale;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, Team $current_team): Response
    {
        $user = $request->user();
        $role = $user->teamRole($current_team);

        $stats = $this->buildStats($current_team, $role);

        return Inertia::render('drinks/dashboard', [
            'stats' => $stats,
            'role' => $role?->value,
        ]);
    }

    public function logs(Request $request, Team $current_team): Response
    {
        $logs = ActivityLog::with('user')
            ->where('team_id', $current_team->id)
            ->latest()
            ->paginate(100);

        return Inertia::render('drinks/logs', [
            'logs' => $logs->items(),
        ]);
    }

    public function settings(Request $request, Team $current_team): Response
    {
        $members = $current_team->members()
            ->withPivot('role', 'poste')
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->pivot->role?->value,
                'roleLabel' => $user->pivot->role?->label(),
                'is_owner' => $user->pivot->role === TeamRole::Owner,
                'blocked_at' => $user->blocked_at,
            ]);

        return Inertia::render('drinks/settings', [
            'members' => $members,
        ]);
    }

    private function buildStats(Team $team, ?TeamRole $role): array
    {
        $tid = $team->id;
        $from = today()->startOfMonth()->toDateString();
        $to = today()->toDateString();

        // Data for charts: last 15 days of sales
        $chartData = Sale::where('team_id', $tid)
            ->validated()
            ->whereBetween('document_date', [today()->subDays(14)->toDateString(), today()->toDateString()])
            ->selectRaw('DATE(document_date) as date, SUM(total_ttc) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'name' => date('d/m', strtotime($row->date)),
                'total' => (float) $row->total,
            ]);

        // Expenses by category for pie chart
        $expenseData = Expense::with('expenseType')
            ->where('team_id', $tid)
            ->validated()
            ->whereBetween('document_date', [$from, $to])
            ->selectRaw('expense_type_id, SUM(amount) as total')
            ->groupBy('expense_type_id')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->expenseType->name ?? 'Autre',
                'value' => (float) $row->total,
            ]);

        $base = [
            'sales_count' => Sale::where('team_id', $tid)->validated()->between($from, $to)->count(),
            'sales_total' => (float) Sale::where('team_id', $tid)->validated()->between($from, $to)->sum('total_ttc'),
            'date_range' => ['from' => $from, 'to' => $to],
            'chart_data' => $chartData,
            'expense_data' => $expenseData,
            'recent_sales' => Sale::with('client')->where('team_id', $tid)->validated()->latest('document_date')->take(5)->get(),
            'low_stock_articles' => Article::where('team_id', $tid)->where('stock_qty', '<', 20)->orderBy('stock_qty')->take(5)->get(),
            'rupture_articles' => Article::where('team_id', $tid)->where('stock_qty', '<=', 0)->orderBy('name')->take(10)->get(),
            'ruptures_count' => Article::where('team_id', $tid)->where('stock_qty', '<=', 0)->count(),
        ];

        return match ($role) {
            TeamRole::Magasinier => array_merge($base, [
                'articles_count' => Article::where('team_id', $tid)->count(),
                'low_stock_count' => Article::where('team_id', $tid)->where('stock_qty', '<=', 10)->count(),
                'inventories_draft' => Inventory::where('team_id', $tid)->where('status', 'draft')->count(),
                'losses_month' => Loss::where('team_id', $tid)->validated()->between($from, $to)->count(),
            ]),
            TeamRole::Caissier => array_merge($base, [
                'payments_total' => (float) Payment::where('team_id', $tid)->between($from, $to)->sum('amount'),
                'cash_inputs_total' => (float) CashInput::where('team_id', $tid)->validated()->between($from, $to)->sum('amount'),
            ]),
            TeamRole::Comptable => array_merge($base, [
                'expenses_total' => (float) Expense::where('team_id', $tid)->validated()->between($from, $to)->sum('amount'),
                'cash_deposits_total' => (float) CashDeposit::where('team_id', $tid)->validated()->between($from, $to)->sum('total_amount'),
                'payments_total' => (float) Payment::where('team_id', $tid)->between($from, $to)->sum('amount'),
            ]),
            TeamRole::Ops => array_merge($base, [
                'procurements_month' => Procurement::where('team_id', $tid)->validated()->between($from, $to)->count(),
                'articles_count' => Article::where('team_id', $tid)->count(),
                'low_stock_count' => Article::where('team_id', $tid)->where('stock_qty', '<=', 10)->count(),
            ]),
            TeamRole::Admin, TeamRole::Owner, TeamRole::Gerant => array_merge($base, [
                'expenses_total' => (float) Expense::where('team_id', $tid)->validated()->between($from, $to)->sum('amount'),
                'payments_total' => (float) Payment::where('team_id', $tid)->between($from, $to)->sum('amount'),
                'procurements_month' => Procurement::where('team_id', $tid)->validated()->between($from, $to)->count(),
                'articles_count' => Article::where('team_id', $tid)->count(),
                'low_stock_count' => Article::where('team_id', $tid)->where('stock_qty', '<=', 10)->count(),
                'losses_month' => Loss::where('team_id', $tid)->validated()->between($from, $to)->count(),
                'cash_deposits_total' => (float) CashDeposit::where('team_id', $tid)->validated()->between($from, $to)->sum('total_amount'),
                'cash_inputs_total' => (float) CashInput::where('team_id', $tid)->validated()->between($from, $to)->sum('amount'),
            ]),
            default => $base, // Rôles restreints ou inconnus : stats minimales
        };
    }
}
