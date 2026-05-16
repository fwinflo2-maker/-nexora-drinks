<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StorePaymentRequest;
use App\Models\Drinks\Payment;
use App\Models\Drinks\Sale;
use App\Models\Team;
use App\Services\Drinks\PaymentService;
use App\Services\Drinks\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $service,
        private readonly PdfService $pdfService,
    ) {}

    public function index(Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', Payment::class);

        $payments = $current_team->drinksPayments()
            ->with(['client', 'sale', 'creator', 'validator'])
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('drinks/payments/index', [
            'payments' => $payments,
        ]);
    }

    public function show(Team $current_team, Payment $payment): InertiaResponse
    {
        Gate::authorize('view', $payment);

        $payment->load(['client', 'sale', 'adjustments', 'creator', 'validator']);

        return Inertia::render('drinks/payments/show', [
            'payment' => $payment,
        ]);
    }

    public function create(Team $current_team): InertiaResponse
    {
        Gate::authorize('create', Payment::class);

        return Inertia::render('drinks/payments/create', [
            'clients' => $current_team->drinksClients()->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'sales' => $current_team->drinksSales()->where('status', TransactionStatus::Validated)
                ->orderByDesc('document_date')
                ->get(['id', 'code', 'total_ttc', 'client_id']),
        ]);
    }

    public function store(StorePaymentRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Payment::class);

        $payment = $current_team->drinksPayments()->create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
            'status' => TransactionStatus::Draft,
        ]);

        // Auto-allocate if a sale was provided
        if ($payment->sale_id) {
            $sale = Sale::find($payment->sale_id);
            if ($sale) {
                $this->service->allocate($payment, $sale);
            }
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Règlement enregistré.')]);

        return to_route('drinks.payments.show', [
            'current_team' => $current_team->slug,
            'payment' => $payment,
        ]);
    }

    public function allocate(Request $request, Team $current_team, Payment $payment): RedirectResponse
    {
        Gate::authorize('update', $payment);

        $request->validate([
            'sale_id' => ['required', 'exists:drinks_sales,id'],
            'observation' => ['nullable', 'string'],
        ]);

        $sale = Sale::findOrFail($request->sale_id);
        $this->service->allocate($payment, $sale, $request->observation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Règlement alloué.')]);

        return to_route('drinks.payments.show', [
            'current_team' => $current_team->slug,
            'payment' => $payment,
        ]);
    }

    public function validatePayment(Team $current_team, Payment $payment): RedirectResponse
    {
        Gate::authorize('validate', $payment);
        abort_if($payment->status !== TransactionStatus::Draft, 422, 'Seuls les brouillons peuvent être validés.');

        $payment->update([
            'status' => TransactionStatus::Validated,
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Règlement validé.')]);

        return to_route('drinks.payments.show', [
            'current_team' => $current_team->slug,
            'payment' => $payment,
        ]);
    }

    public function cancelValidation(Team $current_team, Payment $payment): RedirectResponse
    {
        Gate::authorize('validate', $payment);

        $payment->update([
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.payments.show', [
            'current_team' => $current_team->slug,
            'payment' => $payment,
        ]);
    }

    public function destroy(Team $current_team, Payment $payment): RedirectResponse
    {
        Gate::authorize('delete', $payment);

        $payment->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Règlement supprimé.')]);

        return to_route('drinks.payments.index', ['current_team' => $current_team->slug]);
    }

    public function pdf(Team $current_team, Payment $payment): Response
    {
        Gate::authorize('view', $payment);

        $payment->load(['client', 'sale', 'creator', 'validator']);

        return $this->pdfService->render('drinks.pdf.payment-receipt', [
            'team' => $current_team,
            'payment' => $payment,
        ]);
    }
}
