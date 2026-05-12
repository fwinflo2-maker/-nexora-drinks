<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Models\Drinks\StockMovement;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StockMovementController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', StockMovement::class);

        $movements = StockMovement::with('article')
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(50);

        return Inertia::render('drinks/stock-movements/index', [
            'stockMovements' => $movements,
        ]);
    }

    public function show(Team $current_team, StockMovement $stock_movement): Response
    {
        Gate::authorize('view', $stock_movement);

        $stock_movement->load('article');

        return Inertia::render('drinks/stock-movements/show', [
            'movement' => $stock_movement,
        ]);
    }
}
