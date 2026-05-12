<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\Drinks\ProcurementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreProcurementRequest;
use App\Http\Requests\Drinks\UpdateProcurementRequest;
use App\Models\Drinks\Article;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\Supplier;
use App\Models\Team;
use App\Services\Drinks\PdfService;
use App\Services\Drinks\ProcurementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProcurementController extends Controller
{
    public function __construct(
        private readonly ProcurementService $service,
        private readonly PdfService $pdfService,
    ) {}

    public function index(Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', Procurement::class);

        $procurements = $current_team->drinksProcurements()
            ->with('supplier')
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('drinks/procurements/index', [
            'procurements' => $procurements,
        ]);
    }

    public function show(Team $current_team, Procurement $procurement): InertiaResponse
    {
        Gate::authorize('view', $procurement);

        $procurement->load(['supplier', 'articleLines.article', 'packagingLines.packaging', 'creator', 'validator']);

        return Inertia::render('drinks/procurements/show', [
            'procurement' => $procurement,
        ]);
    }

    public function create(Team $current_team): InertiaResponse
    {
        Gate::authorize('create', Procurement::class);

        return Inertia::render('drinks/procurements/create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'articles' => Article::where('is_active', true)->orderBy('name')->get(['id', 'name', 'cost_price']),
            'kinds' => collect(ProcurementKind::cases())->map(fn ($k) => ['value' => $k->value, 'label' => $k->label()])->values(),
        ]);
    }

    public function store(StoreProcurementRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Procurement::class);

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        $totalHt = collect($lines)->sum(fn ($l) => $l['quantity'] * $l['unit_price']);
        $packsCount = (int) collect($lines)->sum('quantity');

        $procurement = DB::transaction(function () use ($data, $lines, $current_team, $request, $totalHt, $packsCount) {
            $p = Procurement::create([
                ...$data,
                'team_id' => $current_team->id,
                'created_by' => $request->user()->id,
                'status' => TransactionStatus::Draft,
                'total_ht' => $totalHt,
                'packs_count' => $packsCount,
            ]);

            foreach ($lines as $line) {
                $p->articleLines()->create([
                    'article_id' => $line['article_id'],
                    'quantity_received' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'amount' => $line['quantity'] * $line['unit_price'],
                ]);
            }

            return $p;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Approvisionnement créé.')]);

        return to_route('drinks.procurements.show', [
            'current_team' => $current_team->slug,
            'procurement' => $procurement,
        ]);
    }

    public function edit(Team $current_team, Procurement $procurement): InertiaResponse
    {
        Gate::authorize('update', $procurement);

        if (! $procurement->isDraft()) {
            return Inertia::location(route('drinks.procurements.show', [
                'current_team' => $current_team->slug,
                'procurement' => $procurement,
            ]));
        }

        $procurement->load(['articleLines.article']);

        return Inertia::render('drinks/procurements/edit', [
            'procurement' => $procurement,
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'articles' => Article::where('is_active', true)->orderBy('name')->get(['id', 'name', 'cost_price']),
            'kinds' => collect(ProcurementKind::cases())->map(fn ($k) => ['value' => $k->value, 'label' => $k->label()])->values(),
        ]);
    }

    public function update(UpdateProcurementRequest $request, Team $current_team, Procurement $procurement): RedirectResponse
    {
        Gate::authorize('update', $procurement);
        abort_if(! $procurement->isDraft(), 403, 'Seuls les brouillons sont modifiables.');

        $data = $request->validated();
        $lines = $data['lines'];
        unset($data['lines']);

        $totalHt = collect($lines)->sum(fn ($l) => $l['quantity'] * $l['unit_price']);
        $packsCount = (int) collect($lines)->sum('quantity');

        DB::transaction(function () use ($procurement, $data, $lines, $totalHt, $packsCount) {
            $procurement->articleLines()->delete();
            $procurement->update([
                ...$data,
                'total_ht' => $totalHt,
                'packs_count' => $packsCount,
            ]);

            foreach ($lines as $line) {
                $procurement->articleLines()->create([
                    'article_id' => $line['article_id'],
                    'quantity_received' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'amount' => $line['quantity'] * $line['unit_price'],
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Approvisionnement mis à jour.')]);

        return to_route('drinks.procurements.show', [
            'current_team' => $current_team->slug,
            'procurement' => $procurement,
        ]);
    }

    public function destroy(Team $current_team, Procurement $procurement): RedirectResponse
    {
        Gate::authorize('delete', $procurement);
        abort_if(! $procurement->isDraft(), 403, 'Seuls les brouillons sont supprimables.');

        $procurement->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Approvisionnement supprimé.')]);

        return to_route('drinks.procurements.index', [
            'current_team' => $current_team->slug,
        ]);
    }

    public function validateProcurement(Team $current_team, Procurement $procurement): RedirectResponse
    {
        Gate::authorize('validate', $procurement);
        abort_if(! $procurement->isDraft(), 422, 'Seuls les brouillons peuvent être validés.');

        $this->service->validate($procurement, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Approvisionnement validé.')]);

        return to_route('drinks.procurements.show', [
            'current_team' => $current_team->slug,
            'procurement' => $procurement,
        ]);
    }

    public function cancelValidation(Team $current_team, Procurement $procurement): RedirectResponse
    {
        Gate::authorize('validate', $procurement);

        $this->service->cancelValidation($procurement);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Validation annulée.')]);

        return to_route('drinks.procurements.show', [
            'current_team' => $current_team->slug,
            'procurement' => $procurement,
        ]);
    }

    public function pdf(Team $current_team, Procurement $procurement): Response
    {
        Gate::authorize('view', $procurement);

        $procurement->load(['supplier', 'articleLines.article', 'creator', 'validator']);

        return $this->pdfService->render('drinks.pdf.procurement-receipt', [
            'team' => $current_team,
            'procurement' => $procurement,
        ]);
    }
}
