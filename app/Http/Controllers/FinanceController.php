<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    /**
     * Vue principale des finances avec KPIs et dépenses récentes.
     */
    public function index(Request $request, Team $current_team): Response
    {
        $startOfMonth = now()->startOfMonth();

        $totalRevenue = Invoice::where('team_id', $current_team->id)
            ->where('status', 'paid')
            ->sum('total');

        $totalExpenses = Expense::where('team_id', $current_team->id)
            ->sum('amount');

        $revenueThisMonth = Invoice::where('team_id', $current_team->id)
            ->where('status', 'paid')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total');

        $expensesThisMonth = Expense::where('team_id', $current_team->id)
            ->where('date', '>=', $startOfMonth->toDateString())
            ->sum('amount');

        $depenses = Expense::where('team_id', $current_team->id)
            ->latest()
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'label' => $e->description ?? '',
                'amount' => (float) $e->amount,
                'category' => $e->category,
                'date' => $e->date,
            ]);

        $categories = Expense::where('team_id', $current_team->id)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values()
            ->toArray();

        return Inertia::render('finances/index', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'kpis' => [
                'total_revenue' => (float) $totalRevenue,
                'total_expenses' => (float) $totalExpenses,
                'net_balance' => (float) $totalRevenue - (float) $totalExpenses,
                'revenue_this_month' => (float) $revenueThisMonth,
                'expenses_this_month' => (float) $expensesThisMonth,
            ],
            'depenses' => $depenses,
            'categories' => $categories,
        ]);
    }

    /**
     * Enregistrer une dépense opérationnelle.
     */
    public function storeDepense(Request $request, Team $current_team): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        Expense::create([
            'team_id' => $current_team->id,
            'category' => $validated['category'],
            'description' => $validated['label'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Dépense enregistrée.');
    }

    /**
     * Supprimer une dépense.
     */
    public function destroyDepense(Request $request, Team $current_team, Expense $expense): RedirectResponse
    {
        // Le global scope BelongsToTeam garantit que seules les expenses de la team courante
        // sont accessibles via le route model binding. Pas de vérification manuelle nécessaire.
        $expense->delete();

        return back()->with('success', 'Dépense supprimée.');
    }

    /**
     * Rapports financiers agrégés par mois et par catégorie.
     */
    public function rapports(Request $request, Team $current_team): Response
    {
        $twelveMonthsAgo = now()->subMonths(11)->startOfMonth();

        $dateFmtCreatedAt = "DATE_FORMAT(created_at, '%Y-%m') as mois";
        $groupCreatedAt = "DATE_FORMAT(created_at, '%Y-%m')";
        $dateFmtDate = "DATE_FORMAT(date, '%Y-%m') as mois";
        $groupDate = "DATE_FORMAT(date, '%Y-%m')";

        $revenusParMois = Invoice::where('team_id', $current_team->id)
            ->where('status', 'paid')
            ->where('created_at', '>=', $twelveMonthsAgo)
            ->selectRaw("{$dateFmtCreatedAt}, SUM(total) as total")
            ->groupByRaw($groupCreatedAt)
            ->orderBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        $depensesParMois = Expense::where('team_id', $current_team->id)
            ->where('date', '>=', $twelveMonthsAgo->toDateString())
            ->selectRaw("{$dateFmtDate}, SUM(amount) as total")
            ->groupByRaw($groupDate)
            ->orderBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $monthly = $months->map(fn (string $mois) => [
            'month' => $mois,
            'revenue' => (float) ($revenusParMois[$mois] ?? 0),
            'expenses' => (float) ($depensesParMois[$mois] ?? 0),
            'net' => (float) ($revenusParMois[$mois] ?? 0) - (float) ($depensesParMois[$mois] ?? 0),
        ])->values()->toArray();

        $totalExpenses = Expense::where('team_id', $current_team->id)->sum('amount');

        $byCategory = Expense::where('team_id', $current_team->id)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get(['category', 'total'])
            ->map(fn ($row) => [
                'category' => $row->category,
                'total' => (float) $row->total,
                'percentage' => $totalExpenses > 0 ? round((float) $row->total / (float) $totalExpenses * 100, 1) : 0,
            ])
            ->toArray();

        return Inertia::render('finances/rapports', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'monthly' => $monthly,
            'by_category' => $byCategory,
            'year' => now()->year,
        ]);
    }
}
