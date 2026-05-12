<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFactureRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FactureController extends Controller
{
    /**
     * Liste des factures avec stats financières.
     */
    public function index(Request $request, Team $current_team): Response
    {
        $factures = Invoice::where('team_id', $current_team->id)
            ->with(['client'])
            ->latest()
            ->paginate(20)
            ->through(fn ($invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->invoice_number,
                'client_name' => $invoice->client?->name ?? '—',
                'amount' => (float) $invoice->total,
                'status' => $invoice->status,
                'issued_at' => $invoice->created_at?->toDateString(),
                'due_at' => $invoice->due_date,
            ]);

        $totalRevenue = Invoice::where('team_id', $current_team->id)
            ->where('status', 'paid')
            ->sum('total');

        $pendingAmount = Invoice::where('team_id', $current_team->id)
            ->where('status', 'sent')
            ->sum('total');

        $overdueAmount = Invoice::where('team_id', $current_team->id)
            ->where('status', 'overdue')
            ->sum('total');

        $paidCount = Invoice::where('team_id', $current_team->id)
            ->where('status', 'paid')
            ->count();

        $pendingCount = Invoice::where('team_id', $current_team->id)
            ->where('status', 'sent')
            ->count();

        $overdueCount = Invoice::where('team_id', $current_team->id)
            ->where('status', 'overdue')
            ->count();

        $clients = Client::where('team_id', $current_team->id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('factures/index', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'factures' => $factures,
            'clients' => $clients,
            'stats' => [
                'total_revenue' => (float) $totalRevenue,
                'pending_amount' => (float) $pendingAmount,
                'overdue_amount' => (float) $overdueAmount,
                'paid_count' => $paidCount,
                'pending_count' => $pendingCount,
                'overdue_count' => $overdueCount,
            ],
        ]);
    }

    /**
     * Créer une nouvelle facture avec un numéro unique.
     */
    public function store(StoreFactureRequest $request, Team $current_team): RedirectResponse
    {
        $validated = $request->validated();

        $invoiceCount = Invoice::where('team_id', $current_team->id)->withTrashed()->count();
        $invoiceNumber = 'FACT-'.date('Y').'-'.str_pad((string) ($invoiceCount + 1), 4, '0', STR_PAD_LEFT);

        Invoice::create([
            'team_id' => $current_team->id,
            'invoice_number' => $invoiceNumber,
            'client_id' => $validated['client_id'],
            'type' => $validated['type'] ?? 'invoice',
            'status' => 'draft',
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'paid_amount' => 0,
            'due_date' => $validated['due_date'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Facture créée.');
    }

    /**
     * Détail d'une facture avec client et paiements.
     */
    public function show(Request $request, Team $current_team, Invoice $invoice): Response
    {
        abort_if($invoice->team_id !== $current_team->id, 403);

        $invoice->load(['client', 'payments']);

        return Inertia::render('factures/show', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'invoice' => $invoice,
        ]);
    }

    /**
     * Mettre à jour le statut d'une facture.
     */
    public function update(Request $request, Team $current_team, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->team_id !== $current_team->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:draft,sent,paid,partial,overdue,cancelled'],
        ]);

        $invoice->update(['status' => $validated['status']]);

        return back()->with('success', 'Facture mise à jour.');
    }

    /**
     * Supprimer une facture (uniquement si statut = draft).
     */
    public function destroy(Request $request, Team $current_team, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->team_id !== $current_team->id, 403);

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Seules les factures en brouillon peuvent être supprimées.');
        }

        $invoice->delete();

        return back()->with('success', 'Facture supprimée.');
    }

    /**
     * Enregistrer un paiement sur une facture et recalculer le solde.
     */
    public function storePaiement(StorePaymentRequest $request, Team $current_team, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->team_id !== $current_team->id, 403);

        $validated = $request->validated();

        Payment::create([
            'team_id' => $current_team->id,
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'reference' => $validated['reference'] ?? null,
            'received_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        $paidAmount = Payment::where('invoice_id', $invoice->id)->sum('amount');

        $updateData = ['paid_amount' => $paidAmount];

        if ($paidAmount >= (float) $invoice->total) {
            $updateData['status'] = 'paid';
        }

        $invoice->update($updateData);

        return back()->with('success', 'Paiement enregistré.');
    }
}
