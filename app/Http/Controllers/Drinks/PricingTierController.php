<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StorePricingTierRequest;
use App\Http\Requests\Drinks\UpdatePricingTierRequest;
use App\Models\Drinks\Article;
use App\Models\Drinks\PricingTier;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PricingTierController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', PricingTier::class);

        $pricingTiers = PricingTier::with(['article'])->orderBy('label')->get();

        return Inertia::render('drinks/pricing-tiers/index', [
            'pricingTiers' => $pricingTiers,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', PricingTier::class);

        return Inertia::render('drinks/pricing-tiers/create', [
            'articles' => Article::orderBy('name')->get(),
        ]);
    }

    public function store(StorePricingTierRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', PricingTier::class);

        PricingTier::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Grille tarifaire créée.')]);

        return to_route('drinks.pricing-tiers.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function edit(Team $current_team, PricingTier $pricingTier): Response
    {
        Gate::authorize('update', $pricingTier);

        return Inertia::render('drinks/pricing-tiers/edit', [
            'pricingTier' => $pricingTier,
            'articles' => Article::orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePricingTierRequest $request, Team $current_team, PricingTier $pricingTier): RedirectResponse
    {
        Gate::authorize('update', $pricingTier);

        $pricingTier->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Grille tarifaire mise à jour.')]);

        return to_route('drinks.pricing-tiers.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function destroy(Team $current_team, PricingTier $pricingTier): RedirectResponse
    {
        Gate::authorize('delete', $pricingTier);

        $pricingTier->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Grille tarifaire supprimée.')]);

        return to_route('drinks.pricing-tiers.index', [
            'current_team' => $current_team->slug,
        ]);
    }
}
