<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreExpenseRequest;
use App\Http\Requests\Drinks\UpdateExpenseRequest;
use App\Models\Drinks\Expense;
use App\Models\Drinks\ExpenseType;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Expense::class);

        $expenses = $current_team->drinksExpenses()
            ->with('type')
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('drinks/expenses/index', [
            'expenses' => $expenses,
        ]);
    }

    public function show(Team $current_team, Expense $expense): Response
    {
        Gate::authorize('view', $expense);

        $expense->load(['type', 'creator', 'validator']);

        return Inertia::render('drinks/expenses/show', [
            'expense' => $expense,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Expense::class);

        return Inertia::render('drinks/expenses/create', [
            'expenseTypes' => ExpenseType::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(StoreExpenseRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Expense::class);

        $seq = Expense::withoutGlobalScopes()->where('team_id', $current_team->id)->count() + 1;
        $code = 'CHG-'.now()->format('Ymd').'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

        $expense = Expense::create([
            ...$request->validated(),
            'team_id' => $current_team->id,
            'code' => $code,
            'created_by' => $request->user()->id,
            'status' => TransactionStatus::Draft,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Charge créée.')]);

        return to_route('drinks.expenses.show', [
            'current_team' => $current_team->slug,
            'expense' => $expense,
        ]);
    }

    public function edit(Team $current_team, Expense $expense): Response
    {
        Gate::authorize('update', $expense);
        abort_if(! $expense->isDraft(), 403, 'Seuls les brouillons sont modifiables.');

        return Inertia::render('drinks/expenses/edit', [
            'expense' => $expense,
            'expenseTypes' => ExpenseType::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(UpdateExpenseRequest $request, Team $current_team, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);
        abort_if(! $expense->isDraft(), 403, 'Seuls les brouillons sont modifiables.');

        $expense->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Charge mise à jour.')]);

        return to_route('drinks.expenses.show', [
            'current_team' => $current_team->slug,
            'expense' => $expense,
        ]);
    }

    public function destroy(Team $current_team, Expense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);
        abort_if(! $expense->isDraft(), 403, 'Seuls les brouillons sont supprimables.');

        $expense->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Charge supprimée.')]);

        return to_route('drinks.expenses.index', ['current_team' => $current_team->slug]);
    }

    public function validateExpense(Team $current_team, Expense $expense): RedirectResponse
    {
        Gate::authorize('validate', $expense);
        abort_if(! $expense->isDraft(), 422, 'Seuls les brouillons peuvent être validés.');

        $expense->update([
            'status' => TransactionStatus::Validated,
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Charge validée.')]);

        return to_route('drinks.expenses.show', [
            'current_team' => $current_team->slug,
            'expense' => $expense,
        ]);
    }

    public function cancelValidation(Team $current_team, Expense $expense): RedirectResponse
    {
        Gate::authorize('validate', $expense);

        $expense->update([
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.expenses.show', [
            'current_team' => $current_team->slug,
            'expense' => $expense,
        ]);
    }
}
