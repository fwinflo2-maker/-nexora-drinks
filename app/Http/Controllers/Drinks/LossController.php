<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreLossRequest;
use App\Http\Requests\Drinks\UpdateLossRequest;
use App\Models\Drinks\Article;
use App\Models\Drinks\Loss;
use App\Models\Team;
use App\Services\Drinks\LossService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class LossController extends Controller
{
    public function __construct(private readonly LossService $service) {}

    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Loss::class);

        $losses = $current_team->drinksLosses()
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('drinks/losses/index', [
            'losses' => $losses,
        ]);
    }

    public function show(Team $current_team, Loss $loss): Response
    {
        Gate::authorize('view', $loss);

        $loss->load(['lines.article', 'creator', 'validator']);

        return Inertia::render('drinks/losses/show', [
            'loss' => $loss,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Loss::class);

        return Inertia::render('drinks/losses/create', [
            'articles' => Article::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'stock_qty']),
        ]);
    }

    public function store(StoreLossRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Loss::class);

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        $loss = DB::transaction(function () use ($data, $lines, $current_team, $request) {
            $l = Loss::create([
                ...$data,
                'team_id' => $current_team->id,
                'created_by' => $request->user()->id,
                'status' => TransactionStatus::Draft,
            ]);

            foreach ($lines as $line) {
                $l->lines()->create([
                    'article_id' => $line['article_id'],
                    'quantity' => $line['quantity'],
                ]);
            }

            return $l;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Perte créée.')]);

        return to_route('drinks.losses.show', [
            'current_team' => $current_team->slug,
            'loss' => $loss,
        ]);
    }

    public function edit(Team $current_team, Loss $loss): Response
    {
        Gate::authorize('update', $loss);

        if (! $loss->isDraft()) {
            return Inertia::location(route('drinks.losses.show', [
                'current_team' => $current_team->slug,
                'loss' => $loss,
            ]));
        }

        $loss->load(['lines.article']);

        return Inertia::render('drinks/losses/edit', [
            'loss' => $loss,
            'articles' => Article::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'stock_qty']),
        ]);
    }

    public function update(UpdateLossRequest $request, Team $current_team, Loss $loss): RedirectResponse
    {
        Gate::authorize('update', $loss);
        abort_if(! $loss->isDraft(), 403, 'Seuls les brouillons sont modifiables.');

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        DB::transaction(function () use ($loss, $data, $lines) {
            $loss->lines()->delete();
            $loss->update($data);

            foreach ($lines as $line) {
                $loss->lines()->create([
                    'article_id' => $line['article_id'],
                    'quantity' => $line['quantity'],
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Perte mise à jour.')]);

        return to_route('drinks.losses.show', [
            'current_team' => $current_team->slug,
            'loss' => $loss,
        ]);
    }

    public function destroy(Team $current_team, Loss $loss): RedirectResponse
    {
        Gate::authorize('delete', $loss);
        abort_if(! $loss->isDraft(), 403, 'Seuls les brouillons sont supprimables.');

        $loss->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Perte supprimée.')]);

        return to_route('drinks.losses.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function validateLoss(Team $current_team, Loss $loss): RedirectResponse
    {
        Gate::authorize('validate', $loss);
        abort_if(! $loss->isDraft(), 422, 'Seuls les brouillons peuvent être validés.');

        $this->service->validate($loss, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Perte validée.')]);

        return to_route('drinks.losses.show', [
            'current_team' => $current_team->slug,
            'loss' => $loss,
        ]);
    }

    public function cancelValidation(Team $current_team, Loss $loss): RedirectResponse
    {
        Gate::authorize('validate', $loss);

        $this->service->cancelValidation($loss);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.losses.show', [
            'current_team' => $current_team->slug,
            'loss' => $loss,
        ]);
    }
}
