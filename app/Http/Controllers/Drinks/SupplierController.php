<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreSupplierRequest;
use App\Http\Requests\Drinks\UpdateSupplierRequest;
use App\Models\Drinks\Supplier;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Supplier::class);

        $suppliers = Supplier::orderBy('name')->get();

        return Inertia::render('drinks/suppliers/index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Supplier::class);

        return Inertia::render('drinks/suppliers/create');
    }

    public function store(StoreSupplierRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Supplier::class);

        Supplier::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Fournisseur créé.')]);

        return to_route('drinks.suppliers.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function edit(Team $current_team, Supplier $supplier): Response
    {
        Gate::authorize('update', $supplier);

        return Inertia::render('drinks/suppliers/edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Team $current_team, Supplier $supplier): RedirectResponse
    {
        Gate::authorize('update', $supplier);

        $supplier->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Fournisseur mis à jour.')]);

        return to_route('drinks.suppliers.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function destroy(Team $current_team, Supplier $supplier): RedirectResponse
    {
        Gate::authorize('delete', $supplier);

        $supplier->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Fournisseur supprimé.')]);

        return to_route('drinks.suppliers.index', [
            'current_team' => $current_team->slug,
        ]);
    }
}
