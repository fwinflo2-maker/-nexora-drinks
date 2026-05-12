<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreInventoryRequest;
use App\Http\Requests\Drinks\UpdateInventoryRequest;
use App\Models\Drinks\Article;
use App\Models\Drinks\Inventory;
use App\Models\Team;
use App\Services\Drinks\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function __construct(private readonly InventoryService $service) {}

    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Inventory::class);

        $inventories = $current_team->drinksInventories()
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('drinks/inventories/index', [
            'inventories' => $inventories,
        ]);
    }

    public function show(Team $current_team, Inventory $inventory): Response
    {
        Gate::authorize('view', $inventory);

        $inventory->load(['lines.article', 'creator', 'validator']);

        return Inertia::render('drinks/inventories/show', [
            'inventory' => $inventory,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Inventory::class);

        return Inertia::render('drinks/inventories/create', [
            'articles' => Article::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'stock_qty']),
        ]);
    }

    public function store(StoreInventoryRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Inventory::class);

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        $inventory = DB::transaction(function () use ($data, $lines, $current_team, $request) {
            $inv = Inventory::create([
                ...$data,
                'team_id' => $current_team->id,
                'created_by' => $request->user()->id,
                'status' => TransactionStatus::Draft,
            ]);

            foreach ($lines as $line) {
                $inv->lines()->create([
                    'article_id' => $line['article_id'],
                    'counted_qty' => $line['counted_qty'],
                ]);
            }

            return $inv;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Inventaire créé.')]);

        return to_route('drinks.inventories.show', [
            'current_team' => $current_team->slug,
            'inventory' => $inventory,
        ]);
    }

    public function edit(Team $current_team, Inventory $inventory): Response
    {
        Gate::authorize('update', $inventory);

        if (! $inventory->isDraft()) {
            return Inertia::location(route('drinks.inventories.show', [
                'current_team' => $current_team->slug,
                'inventory' => $inventory,
            ]));
        }

        $inventory->load(['lines.article']);

        return Inertia::render('drinks/inventories/edit', [
            'inventory' => $inventory,
            'articles' => Article::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'stock_qty']),
        ]);
    }

    public function update(UpdateInventoryRequest $request, Team $current_team, Inventory $inventory): RedirectResponse
    {
        Gate::authorize('update', $inventory);
        abort_if(! $inventory->isDraft(), 403, 'Seuls les brouillons sont modifiables.');

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        DB::transaction(function () use ($inventory, $data, $lines) {
            $inventory->lines()->delete();
            $inventory->update($data);

            foreach ($lines as $line) {
                $inventory->lines()->create([
                    'article_id' => $line['article_id'],
                    'counted_qty' => $line['counted_qty'],
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Inventaire mis à jour.')]);

        return to_route('drinks.inventories.show', [
            'current_team' => $current_team->slug,
            'inventory' => $inventory,
        ]);
    }

    public function destroy(Team $current_team, Inventory $inventory): RedirectResponse
    {
        Gate::authorize('delete', $inventory);
        abort_if(! $inventory->isDraft(), 403, 'Seuls les brouillons sont supprimables.');

        $inventory->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Inventaire supprimé.')]);

        return to_route('drinks.inventories.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function validateInventory(Team $current_team, Inventory $inventory): RedirectResponse
    {
        Gate::authorize('validate', $inventory);
        abort_if(! $inventory->isDraft(), 422, 'Seuls les brouillons peuvent être validés.');

        $this->service->validate($inventory, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Inventaire validé.')]);

        return to_route('drinks.inventories.show', [
            'current_team' => $current_team->slug,
            'inventory' => $inventory,
        ]);
    }

    public function cancelValidation(Team $current_team, Inventory $inventory): RedirectResponse
    {
        Gate::authorize('validate', $inventory);

        $this->service->cancelValidation($inventory);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.inventories.show', [
            'current_team' => $current_team->slug,
            'inventory' => $inventory,
        ]);
    }
}
