<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreCashDepositRequest;
use App\Models\Drinks\CashDeposit;
use App\Models\Team;
use App\Services\Drinks\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CashDepositController extends Controller
{
    public function __construct(private readonly PdfService $pdfService) {}

    public function index(Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', CashDeposit::class);

        $cashDeposits = $current_team->drinksCashDeposits()
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('drinks/cash-deposits/index', [
            'cashDeposits' => $cashDeposits,
        ]);
    }

    public function show(Team $current_team, CashDeposit $cashDeposit): InertiaResponse
    {
        Gate::authorize('view', $cashDeposit);

        $cashDeposit->load(['creator', 'validator']);

        return Inertia::render('drinks/cash-deposits/show', [
            'cashDeposit' => $cashDeposit,
        ]);
    }

    public function create(Team $current_team): InertiaResponse
    {
        Gate::authorize('create', CashDeposit::class);

        return Inertia::render('drinks/cash-deposits/create');
    }

    public function store(StoreCashDepositRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', CashDeposit::class);

        $data = $request->validated();

        $totalAmount = ($data['amount_cash'] ?? 0) + ($data['amount_cheque'] ?? 0) + ($data['amount_other'] ?? 0);
        $cashDeposit = CashDeposit::create([
            ...$data,
            'team_id' => $current_team->id,
            'total_amount' => $totalAmount,
            'created_by' => $request->user()->id,
            'status' => TransactionStatus::Draft,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Versement caisse créé.')]);

        return to_route('drinks.cash-deposits.show', [
            'current_team' => $current_team->slug,
            'cashDeposit' => $cashDeposit,
        ]);
    }

    public function validateDeposit(Team $current_team, CashDeposit $cashDeposit): RedirectResponse
    {
        Gate::authorize('validate', $cashDeposit);
        abort_if($cashDeposit->status !== TransactionStatus::Draft, 422, 'Seuls les brouillons peuvent être validés.');

        $cashDeposit->update([
            'status' => TransactionStatus::Validated,
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Versement validé.')]);

        return to_route('drinks.cash-deposits.show', [
            'current_team' => $current_team->slug,
            'cashDeposit' => $cashDeposit,
        ]);
    }

    public function cancelValidation(Team $current_team, CashDeposit $cashDeposit): RedirectResponse
    {
        Gate::authorize('validate', $cashDeposit);

        $cashDeposit->update([
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.cash-deposits.show', [
            'current_team' => $current_team->slug,
            'cashDeposit' => $cashDeposit,
        ]);
    }

    public function destroy(Team $current_team, CashDeposit $cashDeposit): RedirectResponse
    {
        Gate::authorize('delete', $cashDeposit);
        abort_if($cashDeposit->status !== TransactionStatus::Draft, 403, 'Seuls les brouillons sont supprimables.');

        $cashDeposit->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Versement supprimé.')]);

        return to_route('drinks.cash-deposits.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function pdf(Team $current_team, CashDeposit $cashDeposit): Response
    {
        Gate::authorize('view', $cashDeposit);

        $cashDeposit->load(['creator', 'validator']);

        return $this->pdfService->render('drinks.pdf.cash-deposit-receipt', [
            'team' => $current_team,
            'cashDeposit' => $cashDeposit,
        ]);
    }
}
