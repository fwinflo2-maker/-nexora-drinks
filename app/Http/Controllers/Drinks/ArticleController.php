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
    public function index(string $current_team): Response
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        Gate::authorize('viewAny', Article::class);

        $articles = $team->articles()
            ->with(['category'])
            ->orderBy('name')
            ->paginate(50);

        return Inertia::render('drinks/articles/index', [
            'articles' => $articles,
        ]);
    }

    public function show(string $current_team, Article $article): Response
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        Gate::authorize('view', $article);

        $article->load(['category', 'packaging']);

        return Inertia::render('drinks/articles/show', [
            'article' => $article,
        ]);
    }

    public function create(string $current_team): Response
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        Gate::authorize('create', Article::class);

        return Inertia::render('drinks/articles/create', [
            'categories' => $team->drinksCategories()->orderBy('name')->get(),
            'packagings' => $team->drinksPackagings()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArticleRequest $request, string $current_team): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        Gate::authorize('create', Article::class);

        $article = $team->articles()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Article créé.')]);

        return to_route('drinks.articles.show', [
            'current_team' => $team->slug,
            'article' => $article->id,
        ]);
    }

    public function edit(string $current_team, Article $article): Response
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        Gate::authorize('update', $article);

        return Inertia::render('drinks/articles/edit', [
            'article' => $article,
            'categories' => $team->drinksCategories()->orderBy('name')->get(),
            'packagings' => $team->drinksPackagings()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateArticleRequest $request, string $current_team, Article $article): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        Gate::authorize('update', $article);

        $article->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Article mis à jour.')]);

        return to_route('drinks.articles.show', [
            'current_team' => $team->slug,
            'article' => $article->id,
        ]);
    }

    public function destroy(string $current_team, Article $article): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        Gate::authorize('delete', $article);

        $article->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Article supprimé.')]);

        return to_route('drinks.articles.index', [
            'current_team' => $team->slug,
        ]);
    }
}
