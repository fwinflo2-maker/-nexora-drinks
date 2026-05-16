<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\SaleKind;
use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreSaleRequest;
use App\Http\Requests\Drinks\UpdateSaleRequest;
use App\Models\Drinks\Sale;
use App\Models\Team;
use App\Services\Drinks\PdfService;
use App\Services\Drinks\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $service,
        private readonly PdfService $pdfService,
    ) {}

    public function index(Request $request, Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', Sale::class);

        $search = $request->get('search');
        $status = $request->get('status');
        $sort = $request->get('sort', 'document_date');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['document_date', 'total_ttc', 'code'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'document_date';
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        $sales = $current_team->drinksSales()
            ->with('client')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy($sort, $direction)
            ->when($sort !== 'id', fn ($q) => $q->orderByDesc('id'))
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('drinks/sales/index', [
            'sales' => $sales,
            'filters' => [
                'search' => $search ?? '',
                'status' => $status ?? '',
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function show(Team $current_team, Sale $sale): InertiaResponse
    {
        Gate::authorize('view', $sale);

        $sale->load(['client', 'articleLines.article', 'packagingLines.packaging', 'creator', 'validator']);

        return Inertia::render('drinks/sales/show', [
            'sale' => $sale,
        ]);
    }

    public function create(Team $current_team): InertiaResponse
    {
        Gate::authorize('create', Sale::class);

        return Inertia::render('drinks/sales/create', [
            'clients' => $current_team->drinksClients()->orderBy('name')->get(['id', 'name']),
            'articles' => $current_team->articles()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'sale_price', 'stock_qty']),
            'kinds' => collect(SaleKind::cases())->map(fn ($k) => ['value' => $k->value, 'label' => $k->label()])->values(),
        ]);
    }

    public function store(StoreSaleRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Sale::class);

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        $totalHt = collect($lines)->sum(fn ($l) => $l['quantity'] * $l['unit_price']);
        $totalTtc = round($totalHt * 1.1925, 2);

        $sale = DB::transaction(function () use ($data, $lines, $current_team, $request, $totalHt, $totalTtc) {
            $s = $current_team->drinksSales()->create([
                ...$data,
                'created_by' => $request->user()->id,
                'status' => TransactionStatus::Draft,
                'total_ht' => $totalHt,
                'total_ttc' => $totalTtc,
            ]);

            foreach ($lines as $line) {
                $amountHt = $line['quantity'] * $line['unit_price'];
                $s->articleLines()->create([
                    'article_id' => $line['article_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'amount_ht' => $amountHt,
                    'amount_ttc' => round($amountHt * 1.1925, 2),
                ]);
            }

            return $s;
        });

        if ($request->boolean('validate') && $request->user()->can('validate', $sale)) {
            $this->service->validate($sale, $request->user()->id);
            Inertia::flash('toast', ['type' => 'success', 'message' => __('Vente créée et validée.')]);
        } else {
            Inertia::flash('toast', ['type' => 'success', 'message' => __('Vente créée (Brouillon).')]);
        }

        return to_route('drinks.sales.show', [
            'current_team' => $current_team->slug,
            'sale' => $sale,
        ]);
    }

    public function edit(Team $current_team, Sale $sale): InertiaResponse
    {
        Gate::authorize('update', $sale);

        if (! $sale->isDraft()) {
            return Inertia::location(route('drinks.sales.show', [
                'current_team' => $current_team->slug,
                'sale' => $sale,
            ]));
        }

        $sale->load(['articleLines.article']);

        return Inertia::render('drinks/sales/edit', [
            'sale' => $sale,
            'clients' => $current_team->drinksClients()->orderBy('name')->get(['id', 'name']),
            'articles' => $current_team->articles()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'sale_price', 'stock_qty']),
            'kinds' => collect(SaleKind::cases())->map(fn ($k) => ['value' => $k->value, 'label' => $k->label()])->values(),
        ]);
    }

    public function update(UpdateSaleRequest $request, Team $current_team, Sale $sale): RedirectResponse
    {
        Gate::authorize('update', $sale);
        abort_if(! $sale->isDraft(), 403, 'Seuls les brouillons sont modifiables.');

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        $totalHt = collect($lines)->sum(fn ($l) => $l['quantity'] * $l['unit_price']);
        $totalTtc = round($totalHt * 1.1925, 2);

        DB::transaction(function () use ($sale, $data, $lines, $totalHt, $totalTtc) {
            $sale->articleLines()->delete();
            $sale->update([
                ...$data,
                'total_ht' => $totalHt,
                'total_ttc' => $totalTtc,
            ]);

            foreach ($lines as $line) {
                $amountHt = $line['quantity'] * $line['unit_price'];
                $sale->articleLines()->create([
                    'article_id' => $line['article_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'amount_ht' => $amountHt,
                    'amount_ttc' => round($amountHt * 1.1925, 2),
                ]);
            }
        });

        if ($request->boolean('validate') && $request->user()->can('validate', $sale)) {
            $this->service->validate($sale, $request->user()->id);
            Inertia::flash('toast', ['type' => 'success', 'message' => __('Vente mise à jour et validée.')]);
        } else {
            Inertia::flash('toast', ['type' => 'success', 'message' => __('Vente mise à jour.')]);
        }

        return to_route('drinks.sales.show', [
            'current_team' => $current_team->slug,
            'sale' => $sale,
        ]);
    }

    public function destroy(Team $current_team, Sale $sale): RedirectResponse
    {
        Gate::authorize('delete', $sale);
        abort_if(! $sale->isDraft(), 403, 'Seuls les brouillons sont supprimables.');

        $sale->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Vente supprimée.')]);

        return to_route('drinks.sales.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function validateSale(Team $current_team, Sale $sale): RedirectResponse
    {
        Gate::authorize('validate', $sale);
        abort_if(! $sale->isDraft(), 422, 'Seuls les brouillons peuvent être validés.');

        $this->service->validate($sale, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Vente validée.')]);

        return to_route('drinks.sales.show', [
            'current_team' => $current_team->slug,
            'sale' => $sale,
        ]);
    }

    public function cancelValidation(Team $current_team, Sale $sale): RedirectResponse
    {
        Gate::authorize('validate', $sale);

        $this->service->cancelValidation($sale);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.sales.show', [
            'current_team' => $current_team->slug,
            'sale' => $sale,
        ]);
    }

    public function pdf(Team $current_team, Sale $sale): Response
    {
        Gate::authorize('view', $sale);

        $sale->load(['client', 'articleLines.article', 'packagingLines.packaging', 'creator', 'validator']);

        return $this->pdfService->render('drinks.pdf.sale-receipt-a4', [
            'team' => $current_team,
            'sale' => $sale,
        ]);
    }
}
