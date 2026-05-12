<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StorePackagingRequest;
use App\Http\Requests\Drinks\UpdatePackagingRequest;
use App\Models\Drinks\Packaging;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PackagingController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Packaging::class);

        $packagings = $current_team->drinksPackagings()
            ->withCount(['articles'])
            ->orderBy('name')
            ->get();

        return Inertia::render('drinks/packagings/index', [
            'packagings' => $packagings,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Packaging::class);

        return Inertia::render('drinks/packagings/create');
    }

    public function store(StorePackagingRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Packaging::class);

        Packaging::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Emballage créé.')]);

        return to_route('drinks.packagings.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function edit(Team $current_team, Packaging $packaging): Response
    {
        Gate::authorize('update', $packaging);

        return Inertia::render('drinks/packagings/edit', [
            'packaging' => $packaging->load(['articles']),
        ]);
    }

    public function update(UpdatePackagingRequest $request, Team $current_team, Packaging $packaging): RedirectResponse
    {
        Gate::authorize('update', $packaging);

        $packaging->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Emballage mis à jour.')]);

        return to_route('drinks.packagings.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function destroy(Team $current_team, Packaging $packaging): RedirectResponse
    {
        Gate::authorize('delete', $packaging);

        $packaging->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Emballage supprimé.')]);

        return to_route('drinks.packagings.index', [
            'current_team' => $current_team->slug,
        ]);
    }
}
