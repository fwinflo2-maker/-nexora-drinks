<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Http\Controllers\Controller;
use App\Http\Requests\FnB\StoreMenuItemRequest;
use App\Http\Requests\FnB\UpdateMenuItemRequest;
use App\Models\FnB\MenuItem;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MenuItemController extends Controller
{
    public function index(Team $current_team): Response
    {
        $items = $current_team->fnbMenuItems()
            ->with('category')
            ->orderBy('name')
            ->get();

        $categories = $current_team->fnbCategories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('fnb/menu-items/index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }

    public function create(Team $current_team): Response
    {
        return Inertia::render('fnb/menu-items/create', [
            'categories' => $current_team->fnbCategories()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreMenuItemRequest $request, Team $current_team): RedirectResponse
    {
        $current_team->fnbMenuItems()->create($request->validated());

        return to_route('fnb.menu-items.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Article créé.']);
    }

    public function edit(Team $current_team, MenuItem $menuItem): Response
    {
        return Inertia::render('fnb/menu-items/edit', [
            'item' => $menuItem,
            'categories' => $current_team->fnbCategories()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateMenuItemRequest $request, Team $current_team, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update($request->validated());

        return to_route('fnb.menu-items.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Article mis à jour.']);
    }

    public function destroy(Team $current_team, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return to_route('fnb.menu-items.index', $current_team->slug)
            ->with('toast', ['type' => 'success', 'message' => 'Article supprimé.']);
    }

    public function toggleAvailability(Team $current_team, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Disponibilité mise à jour.']);
    }
}
