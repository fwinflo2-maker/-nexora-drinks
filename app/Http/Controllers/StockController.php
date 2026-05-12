<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Team;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    /**
     * Vue principale des niveaux de stock.
     */
    public function index(Request $request, Team $current_team): Response
    {
        $products = Product::where('team_id', $current_team->id)
            ->with(['category:id,name', 'stockLevels'])
            ->withSum('stockLevels as total_stock', 'quantity')
            ->withMin('stockLevels as min_stock_level', 'min_threshold')
            ->latest()
            ->paginate(25)
            ->through(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'stock_quantity' => (int) ($p->total_stock ?? 0),
                'min_stock_level' => (int) ($p->min_stock_level ?? 0),
                'unit' => $p->unit ?? 'unité',
                'category' => $p->category?->name,
            ]);

        $warehouses = Warehouse::where('team_id', $current_team->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'address']);

        $totalProducts = Product::where('team_id', $current_team->id)->count();

        $lowStockCount = StockLevel::where('team_id', $current_team->id)
            ->whereNotNull('min_threshold')
            ->whereRaw('quantity <= min_threshold AND quantity > 0')
            ->distinct('product_id')
            ->count('product_id');

        $outOfStockCount = StockLevel::where('team_id', $current_team->id)
            ->where('quantity', '<=', 0)
            ->distinct('product_id')
            ->count('product_id');

        return Inertia::render('stocks/index', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'products' => $products,
            'warehouses' => $warehouses,
            'stats' => [
                'total' => $totalProducts,
                'low_stock' => $lowStockCount,
                'out_of_stock' => $outOfStockCount,
            ],
        ]);
    }

    /**
     * Liste des mouvements de stock.
     */
    public function mouvements(Request $request, Team $current_team): Response
    {
        $mouvements = StockMovement::where('team_id', $current_team->id)
            ->with(['product', 'warehouse', 'creator'])
            ->latest()
            ->paginate(30);

        return Inertia::render('stocks/mouvements', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'mouvements' => $mouvements,
        ]);
    }

    /**
     * Enregistrer un nouveau mouvement de stock.
     */
    public function storeMovement(StoreStockMovementRequest $request, Team $current_team): RedirectResponse
    {
        $validated = $request->validated();

        StockMovement::create([
            'team_id' => $current_team->id,
            'product_id' => $validated['product_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'movement_type' => $validated['movement_type'],
            'quantity' => $validated['quantity'],
            'unit_cost' => $validated['unit_cost'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $stockLevel = StockLevel::firstOrCreate(
            [
                'team_id' => $current_team->id,
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['warehouse_id'],
            ],
            ['quantity' => 0, 'reserved_quantity' => 0],
        );

        $delta = (int) $validated['quantity'];

        if ($validated['movement_type'] === 'in' || $validated['movement_type'] === 'adjustment') {
            $stockLevel->increment('quantity', $delta);
        } else {
            $stockLevel->decrement('quantity', $delta);
        }

        return back()->with('success', 'Mouvement enregistré.');
    }

    /**
     * Vue de rangement des produits dans les entrepôts.
     */
    public function rangement(Request $request, Team $current_team): Response
    {
        $warehouses = Warehouse::where('team_id', $current_team->id)
            ->with(['stockLevels.product'])
            ->get();

        return Inertia::render('stocks/rangement', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Mettre à jour le rangement.
     */
    public function updateRangement(Request $request, Team $current_team): RedirectResponse
    {
        return back()->with('success', 'Rangement mis à jour.');
    }

    /**
     * Vue picking — préparation des commandes.
     */
    public function picking(Request $request, Team $current_team): Response
    {
        $pickingLists = DeliveryRoute::where('team_id', $current_team->id)
            ->where('status', 'planned')
            ->where('date', '>=', now()->toDateString())
            ->with(['deliveries.order.items.product'])
            ->orderBy('date')
            ->get();

        return Inertia::render('stocks/picking', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'picking_lists' => $pickingLists,
        ]);
    }

    /**
     * Confirmer le picking d'une commande.
     */
    public function confirmPicking(Request $request, Team $current_team, Order $order): RedirectResponse
    {
        return back()->with('success', 'Picking confirmé.');
    }
}
