<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreCashInputRequest;
use App\Models\Drinks\CashInput;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CashInputController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', CashInput::class);

        $cashInputs = $current_team->drinksCashInputs()
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('drinks/cash-inputs/index', [
            'cashInputs' => $cashInputs,
        ]);
    }

    public function show(Team $current_team, CashInput $cashInput): Response
    {
        Gate::authorize('view', $cashInput);

        $cashInput->load(['creator', 'validator']);

        return Inertia::render('drinks/cash-inputs/show', [
            'cashInput' => $cashInput,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', CashInput::class);

        return Inertia::render('drinks/cash-inputs/create');
    }

    public function store(StoreCashInputRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', CashInput::class);

        $cashInput = CashInput::create([
            ...$request->validated(),
            'team_id' => $current_team->id,
            'created_by' => $request->user()->id,
            'status' => TransactionStatus::Draft,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Apport caisse créé.')]);

        return to_route('drinks.cash-inputs.show', [
            'current_team' => $current_team->slug,
            'cashInput' => $cashInput,
        ]);
    }

    public function validateCashInput(Team $current_team, CashInput $cashInput): RedirectResponse
    {
        Gate::authorize('validate', $cashInput);
        abort_if($cashInput->status !== TransactionStatus::Draft, 422, 'Seuls les brouillons peuvent être validés.');

        $cashInput->update([
            'status' => TransactionStatus::Validated,
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Apport validé.')]);

        return to_route('drinks.cash-inputs.show', [
            'current_team' => $current_team->slug,
            'cashInput' => $cashInput,
        ]);
    }

    public function cancelValidation(Team $current_team, CashInput $cashInput): RedirectResponse
    {
        Gate::authorize('validate', $cashInput);

        $cashInput->update([
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.cash-inputs.show', [
            'current_team' => $current_team->slug,
            'cashInput' => $cashInput,
        ]);
    }

    public function destroy(Team $current_team, CashInput $cashInput): RedirectResponse
    {
        Gate::authorize('delete', $cashInput);
        abort_if($cashInput->status !== TransactionStatus::Draft, 403, 'Seuls les brouillons sont supprimables.');

        $cashInput->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Apport supprimé.')]);

        return to_route('drinks.cash-inputs.index', [
            'current_team' => $current_team->slug,
        ]);
    }
}
