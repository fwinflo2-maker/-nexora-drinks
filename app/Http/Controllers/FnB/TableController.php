<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Http\Controllers\Controller;
use App\Http\Requests\FnB\StoreTableRequest;
use App\Http\Requests\FnB\UpdateTableRequest;
use App\Models\FnB\Table;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index(Team $current_team): Response
    {
        $tables = $current_team->fnbTables()
            ->withCount(['orders' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get();

        return Inertia::render('fnb/tables/index', [
            'tables' => $tables,
        ]);
    }

    public function store(StoreTableRequest $request, Team $current_team): RedirectResponse
    {
        $data = $request->validated();
        $current_team->fnbTables()->create($data + ['status' => 'free']);

        return back()->with('toast', ['type' => 'success', 'message' => "Table {$data['name']} créée."]);
    }

    public function update(UpdateTableRequest $request, Team $current_team, Table $table): RedirectResponse
    {
        $table->update($request->validated());

        return back()->with('toast', ['type' => 'success', 'message' => 'Table mise à jour.']);
    }

    public function destroy(Team $current_team, Table $table): RedirectResponse
    {
        $table->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Table supprimée.']);
    }
}
