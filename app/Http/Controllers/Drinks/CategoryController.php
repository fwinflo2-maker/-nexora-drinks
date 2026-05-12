<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreCategoryRequest;
use App\Http\Requests\Drinks\UpdateCategoryRequest;
use App\Models\Drinks\Category;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Category::class);

        $categories = $current_team->drinksCategories()->orderBy('name')->get();

        return Inertia::render('drinks/categories/index', [
            'categories' => $categories,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Category::class);

        return Inertia::render('drinks/categories/create');
    }

    public function store(StoreCategoryRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Category::class);

        $current_team->drinksCategories()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Catégorie créée.')]);

        return to_route('drinks.categories.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function edit(Team $current_team, Category $category): Response
    {
        Gate::authorize('update', $category);

        return Inertia::render('drinks/categories/edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Team $current_team, Category $category): RedirectResponse
    {
        Gate::authorize('update', $category);

        $category->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Catégorie mise à jour.')]);

        return to_route('drinks.categories.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function destroy(Team $current_team, Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Catégorie supprimée.')]);

        return to_route('drinks.categories.index', [
            'current_team' => $current_team->slug,
        ]);
    }
}
