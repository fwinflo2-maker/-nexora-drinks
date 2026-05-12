<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Models\Drinks\StockSnapshot;
use App\Models\Team;
use App\Services\Drinks\SnapshotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StockSnapshotController extends Controller
{
    public function __construct(
        private readonly SnapshotService $snapshotService
    ) {}

    /**
     * Liste les dates de snapshot disponibles pour la team, avec agrégats par date.
     */
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', StockSnapshot::class);

        $dates = StockSnapshot::selectRaw('snapshot_date, COUNT(article_id) as article_count, SUM(stock_qty) as total_stock_qty, MAX(updated_at) as last_updated')
            ->groupBy('snapshot_date')
            ->orderByDesc('snapshot_date')
            ->paginate(20);

        return Inertia::render('drinks/stock-snapshots/index', [
            'dates' => $dates,
        ]);
    }

    /**
     * Affiche tous les snapshots pour une date donnée, avec l'article eager loadé.
     */
    public function show(Team $current_team, string $date): Response
    {
        Gate::authorize('viewAny', StockSnapshot::class);

        $snapshots = StockSnapshot::with('article')
            ->where('snapshot_date', $date)
            ->orderBy('snapshot_date')
            ->get()
            ->sortBy(fn (StockSnapshot $s) => $s->article?->name)
            ->values();

        return Inertia::render('drinks/stock-snapshots/show', [
            'snapshots' => $snapshots,
            'date' => $date,
        ]);
    }

    /**
     * Déclenche un snapshot manuel pour la date du jour.
     */
    public function store(Request $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('viewAny', StockSnapshot::class);

        $count = $this->snapshotService->take($current_team);

        return back()->with('success', "{$count} articles ont été archivés avec succès.");
    }
}
