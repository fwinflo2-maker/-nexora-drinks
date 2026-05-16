<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Http\Controllers\Controller;
use App\Http\Requests\FnB\StoreCategoryRequest;
use App\Http\Requests\FnB\UpdateCategoryRequest;
use App\Models\FnB\Category;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Team $current_team): Response
    {
        $categories = $current_team->fnbCategories()
            ->withCount('menuItems')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('fnb/categories/index', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreCategoryRequest $request, Team $current_team): RedirectResponse
    {
        $current_team->fnbCategories()->create($request->validated());

        return back()->with('toast', ['type' => 'success', 'message' => 'Catégorie créée.']);
    }

    public function update(UpdateCategoryRequest $request, Team $current_team, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return back()->with('toast', ['type' => 'success', 'message' => 'Catégorie mise à jour.']);
    }

    public function destroy(Team $current_team, Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Catégorie supprimée.']);
    }
}
