<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\StoreClientRequest;
use App\Http\Requests\Drinks\UpdateClientRequest;
use App\Models\Drinks\Client;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Team $current_team): Response
    {
        Gate::authorize('viewAny', Client::class);

        $clients = $current_team->drinksClients()->orderBy('name')->paginate(20);

        return Inertia::render('drinks/clients/index', [
            'clients' => $clients,
        ]);
    }

    public function show(Team $current_team, Client $client): Response
    {
        Gate::authorize('view', $client);

        return Inertia::render('drinks/clients/show', [
            'client' => $client,
        ]);
    }

    public function create(Team $current_team): Response
    {
        Gate::authorize('create', Client::class);

        return Inertia::render('drinks/clients/create');
    }

    public function store(StoreClientRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', Client::class);

        $client = $current_team->drinksClients()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Client créé.')]);

        return to_route('drinks.clients.show', [
            'current_team' => $current_team->slug,
            'client' => $client,
        ]);
    }

    public function edit(Team $current_team, Client $client): Response
    {
        Gate::authorize('update', $client);

        return Inertia::render('drinks/clients/edit', [
            'client' => $client,
        ]);
    }

    public function update(UpdateClientRequest $request, Team $current_team, Client $client): RedirectResponse
    {
        Gate::authorize('update', $client);

        $client->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Client mis à jour.')]);

        return to_route('drinks.clients.show', [
            'current_team' => $current_team->slug,
            'client' => $client,
        ]);
    }

    public function quickStore(Request $request, Team $current_team): JsonResponse
    {
        Gate::authorize('create', Client::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $client = $current_team->drinksClients()->create($validated);

        return response()->json(['id' => $client->id, 'name' => $client->name]);
    }

    public function destroy(Team $current_team, Client $client): RedirectResponse
    {
        Gate::authorize('delete', $client);

        $client->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Client supprimé.')]);

        return to_route('drinks.clients.index', [
            'current_team' => $current_team->slug,
        ]);
    }
}
