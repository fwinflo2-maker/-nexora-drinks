<?php

namespace App\Http\Controllers;

use App\Enums\TeamRole;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class EquipeController extends Controller
{
    /**
     * Vue de gestion des membres de l'équipe.
     */
    public function index(Request $request, Team $current_team): Response
    {
        $membres = $current_team->members()
            ->withPivot('role', 'poste', 'extra_roles')
            ->orderBy('name')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'role' => $m->pivot->role instanceof \UnitEnum ? $m->pivot->role->value : $m->pivot->role,
                'poste' => $m->pivot->poste,
                'extra_roles' => $m->pivot->extra_roles ?? [],
                'joined_at' => $m->pivot->created_at?->toDateString(),
            ]);

        return Inertia::render('equipe/index', [
            'team' => $current_team->only('id', 'name', 'slug'),
            'members' => $membres,
            'roles' => TeamRole::cases(),
        ]);
    }

    /**
     * Ajouter un membre existant à l'équipe.
     */
    public function store(Request $request, Team $current_team): RedirectResponse
    {
        $teamRoleValues = array_map(fn (TeamRole $role) => $role->value, TeamRole::cases());

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::in($teamRoleValues)],
            'poste' => ['nullable', 'string', 'max:100'],
            'extra_roles' => ['nullable', 'array'],
            'extra_roles.*' => ['string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($current_team->members()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['email' => 'Cet utilisateur est déjà membre de l\'équipe.']);
        }

        $current_team->members()->attach($user->id, [
            'role' => $validated['role'],
            'poste' => $validated['poste'] ?? null,
            'extra_roles' => isset($validated['extra_roles']) ? json_encode($validated['extra_roles']) : null,
        ]);

        return back()->with('success', 'Membre ajouté.');
    }

    /**
     * Modifier le rôle d'un membre.
     */
    public function update(Request $request, Team $current_team, Membership $membership): RedirectResponse
    {
        abort_if($membership->team_id !== $current_team->id, 403);

        $teamRoleValues = array_map(fn (TeamRole $role) => $role->value, TeamRole::cases());

        $validated = $request->validate([
            'role' => ['required', Rule::in($teamRoleValues)],
            'poste' => ['nullable', 'string', 'max:100'],
            'extra_roles' => ['nullable', 'array'],
            'extra_roles.*' => ['string'],
        ]);

        $membership->update([
            'role' => $validated['role'],
            'poste' => $validated['poste'] ?? $membership->poste,
            'extra_roles' => isset($validated['extra_roles']) ? json_encode($validated['extra_roles']) : $membership->extra_roles,
        ]);

        return back()->with('success', 'Rôle mis à jour.');
    }

    /**
     * Retirer un membre de l'équipe.
     */
    public function destroy(Request $request, Team $current_team, Membership $membership): RedirectResponse
    {
        abort_if($membership->team_id !== $current_team->id, 403);
        abort_if($membership->role === TeamRole::Owner, 403);

        $current_team->members()->detach($membership->user_id);

        return back()->with('success', 'Membre retiré.');
    }
}
