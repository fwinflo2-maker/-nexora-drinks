<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreArticleRequest;
use App\Http\Requests\Drinks\UpdateArticleRequest;
use App\Models\Drinks\Article;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Article::class);

        $articles = $current_team->articles()
            ->with(['category'])
            ->orderBy('name')
            ->paginate(50);

        return Inertia::render('drinks/articles/index', [
            'articles' => $articles,
        ]);
    }

    public function show(Team $current_team, Article $article): Response
    {
        Gate::authorize('view', $article);

        $article->load(['category', 'packaging']);

        return Inertia::render('drinks/articles/show', [
            'article' => $article,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Article::class);

        return Inertia::render('drinks/articles/create', [
            'categories' => $current_team->drinksCategories()->orderBy('name')->get(),
            'packagings' => $current_team->drinksPackagings()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArticleRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Article::class);

        $article = $current_team->articles()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Article créé.')]);

        return to_route('drinks.articles.show', [
            'current_team' => $current_team->slug,
            'article' => $article->id,
        ]);
    }

    public function edit(Team $current_team, Article $article): Response
    {
        Gate::authorize('update', $article);

        return Inertia::render('drinks/articles/edit', [
            'article' => $article,
            'categories' => $current_team->drinksCategories()->orderBy('name')->get(),
            'packagings' => $current_team->drinksPackagings()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateArticleRequest $request, Team $current_team, Article $article): RedirectResponse
    {
        Gate::authorize('update', $article);

        $article->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Article mis à jour.')]);

        return to_route('drinks.articles.show', [
            'current_team' => $current_team->slug,
            'article' => $article->id,
        ]);
    }

    public function destroy(Team $current_team, Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);

        $article->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Article supprimé.')]);

        return to_route('drinks.articles.index', [
            'current_team' => $current_team->slug,
        ]);
    }
}
