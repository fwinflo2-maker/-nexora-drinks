<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPackagingBalance;
use App\Models\PackagingMovement;
use App\Models\PackagingType;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ConsignmentController extends Controller
{
    /**
     * Affiche le tableau de bord des consignes (Dette Emballage).
     */
    public function index(Request $request, Team $current_team)
    {
        $team = $current_team;

        // Récupérer les soldes de consignes avec les infos client et type d'emballage
        $balances = ClientPackagingBalance::with(['client', 'packagingType'])
            ->where('team_id', $team->id)
            ->where('quantity_owed', '>', 0)
            ->orderByDesc('quantity_owed')
            ->get()
            ->map(function ($balance) {
                return [
                    'id' => $balance->id,
                    'client_name' => $balance->client->name,
                    'packaging_name' => $balance->packagingType->name,
                    'quantity_owed' => $balance->quantity_owed,
                    'value_xaf' => $balance->quantity_owed * $balance->packagingType->unit_value_xaf,
                    'last_updated_at' => $balance->last_updated_at?->format('d/m/Y H:i') ?? '-',
                ];
            });

        // Les types d'emballages pour le formulaire de retour
        $packagingTypes = PackagingType::where('team_id', $team->id)->where('is_active', true)->get(['id', 'name', 'unit_value_xaf']);

        // Les clients pour le formulaire
        $clients = Client::where('team_id', $team->id)->where('is_active', true)->get(['id', 'name']);

        // Mouvements récents
        $recentMovements = PackagingMovement::with(['client', 'packagingType', 'creator'])
            ->where('team_id', $team->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'client' => $movement->client->name,
                    'packaging' => $movement->packagingType->name,
                    'type' => $movement->movement_type === 'out' ? 'Prêt (Sortie)' : 'Retour (Entrée)',
                    'quantity' => $movement->quantity,
                    'date' => $movement->created_at->format('d/m/Y H:i'),
                    'user' => $movement->creator->name,
                ];
            });

        return Inertia::render('consignments/index', [
            'balances' => $balances,
            'packagingTypes' => $packagingTypes,
            'clients' => $clients,
            'recentMovements' => $recentMovements,
            'stats' => [
                'total_owed' => $balances->sum('quantity_owed'),
                'total_value_xaf' => $balances->sum('value_xaf'),
            ],
        ]);
    }

    /**
     * Enregistrer un nouveau mouvement d'emballage (retour consignes).
     */
    public function storeMovement(Request $request, Team $current_team)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'packaging_type_id' => 'required|exists:packaging_types,id',
            'movement_type' => 'required|in:out,in',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $teamId = $current_team->id;

        DB::transaction(function () use ($validated, $teamId, $request) {
            // 1. Créer le mouvement
            PackagingMovement::create([
                'team_id' => $teamId,
                'client_id' => $validated['client_id'],
                'packaging_type_id' => $validated['packaging_type_id'],
                'movement_type' => $validated['movement_type'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            // 2. Mettre à jour le solde (Dette)
            $balance = ClientPackagingBalance::firstOrCreate(
                [
                    'team_id' => $teamId,
                    'client_id' => $validated['client_id'],
                    'packaging_type_id' => $validated['packaging_type_id'],
                ],
                ['quantity_owed' => 0]
            );

            if ($validated['movement_type'] === 'out') {
                // Livré au client = dette augmente
                $balance->increment('quantity_owed', $validated['quantity']);
            } else {
                // Retourné par le client = dette diminue
                // On s'assure que la dette ne devienne pas négative, bien que parfois possible (avance)
                $newOwed = max(0, $balance->quantity_owed - $validated['quantity']);
                $balance->update(['quantity_owed' => $newOwed, 'last_updated_at' => now()]);
            }
        });

        return back()->with('success', 'Mouvement de consignes enregistré avec succès.');
    }
}
