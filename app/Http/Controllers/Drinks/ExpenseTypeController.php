<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreExpenseTypeRequest;
use App\Http\Requests\Drinks\UpdateExpenseTypeRequest;
use App\Models\Drinks\ExpenseType;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseTypeController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', ExpenseType::class);

        $types = ExpenseType::orderBy('name')->paginate(20);

        return Inertia::render('drinks/expense-types/index', [
            'types' => $types,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', ExpenseType::class);

        return Inertia::render('drinks/expense-types/create');
    }

    public function store(StoreExpenseTypeRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', ExpenseType::class);

        $type = ExpenseType::create([
            ...$request->validated(),
            'team_id' => $current_team->id,
            'is_active' => true,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Type de charge créé.')]);

        return to_route('drinks.expense-types.index', ['current_team' => $current_team->slug]);
    }

    public function edit(Team $current_team, ExpenseType $expenseType): Response
    {
        Gate::authorize('update', $expenseType);

        return Inertia::render('drinks/expense-types/edit', [
            'type' => $expenseType,
        ]);
    }

    public function update(UpdateExpenseTypeRequest $request, Team $current_team, ExpenseType $expenseType): RedirectResponse
    {
        Gate::authorize('update', $expenseType);

        $expenseType->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Type de charge mis à jour.')]);

        return to_route('drinks.expense-types.index', ['current_team' => $current_team->slug]);
    }

    public function destroy(Team $current_team, ExpenseType $expenseType): RedirectResponse
    {
        Gate::authorize('delete', $expenseType);

        $expenseType->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Type de charge supprimé.')]);

        return to_route('drinks.expense-types.index', ['current_team' => $current_team->slug]);
    }
}
