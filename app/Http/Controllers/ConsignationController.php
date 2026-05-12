<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsignationMovementRequest;
use App\Models\Client;
use App\Models\ClientPackagingBalance;
use App\Models\PackagingMovement;
use App\Models\PackagingType;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConsignationController extends Controller
{
    /**
     * Vue principale des consignations (dette emballage).
     */
    public function index(Request $request, Team $current_team): Response
    {
        $clients = Client::where('team_id', $current_team->id)
            ->with(['packagingBalances.packagingType'])
            ->orderBy('name')
            ->get();

        $packagingTypes = PackagingType::where('team_id', $current_team->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'unit_value_xaf']);

        return Inertia::render('consignations/index', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'clients' => $clients,
            'packaging_types' => $packagingTypes,
        ]);
    }

    /**
     * Enregistrer un type d'emballage.
     */
    public function store(Request $request, Team $current_team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_value_xaf' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        PackagingType::create([
            'team_id' => $current_team->id,
            'name' => $validated['name'],
            'unit_value_xaf' => $validated['unit_value_xaf'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Type d\'emballage créé.');
    }

    /**
     * Vue détaillée des consignations d'un client.
     */
    public function show(Request $request, Team $current_team, Client $client): Response
    {
        $historique = PackagingMovement::where('team_id', $current_team->id)
            ->where('client_id', $client->id)
            ->with(['packagingType', 'creator'])
            ->latest()
            ->paginate(20);

        $balances = ClientPackagingBalance::where('team_id', $current_team->id)
            ->where('client_id', $client->id)
            ->with('packagingType')
            ->get();

        return Inertia::render('consignations/show', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'client' => $client->only('id', 'name', 'phone', 'address'),
            'historique' => $historique,
            'balances' => $balances,
        ]);
    }

    /**
     * Enregistrer un mouvement de consignation pour un client.
     */
    public function storeMovement(StoreConsignationMovementRequest $request, Team $current_team, Client $client): RedirectResponse
    {
        $validated = $request->validated();

        PackagingMovement::create([
            'team_id' => $current_team->id,
            'client_id' => $client->id,
            'packaging_type_id' => $validated['packaging_type_id'],
            'movement_type' => $validated['movement_type'],
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $balance = ClientPackagingBalance::firstOrCreate(
            [
                'team_id' => $current_team->id,
                'client_id' => $client->id,
                'packaging_type_id' => $validated['packaging_type_id'],
            ],
            ['quantity_owed' => 0, 'last_updated_at' => now()],
        );

        if ($validated['movement_type'] === 'out') {
            $balance->increment('quantity_owed', (int) $validated['quantity']);
        } else { // 'in' = retour
            $balance->decrement('quantity_owed', (int) $validated['quantity']);
        }

        $balance->update(['last_updated_at' => now()]);

        return back()->with('success', 'Mouvement de consignation enregistré.');
    }
}
