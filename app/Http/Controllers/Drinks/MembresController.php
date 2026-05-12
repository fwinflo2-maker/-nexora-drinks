<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\TeamRole;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MembresController extends Controller
{
    /** Drinks-relevant assignable roles. */
    private const DRINKS_ROLES = [
        TeamRole::Gerant,
        TeamRole::Ops,
        TeamRole::Magasinier,
        TeamRole::Caissier,
        TeamRole::Comptable,
    ];

    private function roleValues(): string
    {
        return implode(',', array_map(fn (TeamRole $r) => $r->value, self::DRINKS_ROLES));
    }

    public function index(Request $request, string $current_team): Response
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $this->authorizeAdmin($request->user(), $team);

        $members = $team->members()
            ->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $this->roleValue($u->pivot->role),
                'roleLabel' => $this->roleEnum($u->pivot->role)?->label() ?? $this->roleValue($u->pivot->role),
                'is_owner' => $u->id === $team->user_id,
                'is_admin' => $u->teamRole($team) === TeamRole::Admin,
                'blocked_at' => $u->blocked_at?->toIso8601String(),
            ]);

        return Inertia::render('drinks/dashboard', [
            '_module' => 'membres',
            '_action' => 'index',
            'members' => $members,
            'roles' => collect(self::DRINKS_ROLES)
                ->map(fn (TeamRole $r) => ['value' => $r->value, 'label' => $r->label()])
                ->toArray(),
        ]);
    }

    public function store(Request $request, string $current_team): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $this->authorizeAdmin($request->user(), $team);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:'.$this->roleValues()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'current_team_id' => $current_team->id, // Assurer que le dashboard Drinks se charge directement
        ]);

        $current_team->members()->attach($user->id, ['role' => $validated['role']]);

        return back()->with('success', "Profil créé et ajouté à l'équipe.");
    }

    public function updateRole(Request $request, string $current_team, User $user): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $this->authorizeAdmin($request->user(), $team);

        $pivotRole = $team->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;

        if ($this->roleEnum($pivotRole) === TeamRole::Owner || $this->roleEnum($pivotRole) === TeamRole::Admin) {
            return back()->withErrors(['role' => 'Impossible de modifier le rôle d\'un administrateur ou du propriétaire.']);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:'.$this->roleValues()],
        ]);

        $team->members()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        return back()->with('success', 'Rôle mis à jour.');
    }

    public function updatePassword(Request $request, string $current_team, User $user): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $this->authorizeAdmin($request->user(), $team);

        $pivotRole = $team->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;

        if ($this->roleEnum($pivotRole) === TeamRole::Owner || $this->roleEnum($pivotRole) === TeamRole::Admin) {
            return back()->withErrors(['password' => 'Impossible de modifier le mot de passe d\'un administrateur ou du propriétaire.']);
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Mot de passe mis à jour.');
    }

    public function block(Request $request, string $current_team, User $user): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $actor = $request->user();
        $this->authorizeAdmin($actor, $team);

        $pivotRole = $team->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;

        if ($this->roleEnum($pivotRole) === TeamRole::Owner || $this->roleEnum($pivotRole) === TeamRole::Admin || $actor->id === $user->id) {
            return back()->withErrors(['block' => 'Action non autorisée sur un administrateur ou vous-même.']);
        }

        $user->update(['blocked_at' => now()]);

        return back()->with('success', 'Profil bloqué.');
    }

    public function unblock(Request $request, string $current_team, User $user): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $this->authorizeAdmin($request->user(), $team);

        $user->update(['blocked_at' => null]);

        return back()->with('success', 'Profil débloqué.');
    }

    public function updateProfile(Request $request, string $current_team, User $user): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $this->authorizeAdmin($request->user(), $team);

        $pivotRole = $team->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;

        if ($this->roleEnum($pivotRole) === TeamRole::Owner || $this->roleEnum($pivotRole) === TeamRole::Admin) {
            return back()->withErrors(['profile' => 'Impossible de modifier le profil d\'un administrateur ou du propriétaire.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour.');
    }

    public function remove(Request $request, string $current_team, User $user): RedirectResponse
    {
        $team = Team::where('slug', $current_team)->firstOrFail();
        $actor = $request->user();
        $this->authorizeAdmin($actor, $team);

        $pivotRole = $team->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;

        if ($this->roleEnum($pivotRole) === TeamRole::Owner || $this->roleEnum($pivotRole) === TeamRole::Admin) {
            return back()->withErrors(['remove' => 'Impossible de retirer un administrateur ou le propriétaire.']);
        }

        if ($actor->id === $user->id) {
            return back()->withErrors(['remove' => 'Vous ne pouvez pas vous retirer vous-même.']);
        }

        $team->members()->detach($user->id);

        return back()->with('success', 'Membre retiré.');
    }

    private function authorizeAdmin(User $user, Team $team): void
    {
        $role = $user->teamRole($team);

        if ($role !== TeamRole::Admin && $role !== TeamRole::Owner) {
            abort(403);
        }
    }

    /** Normalize pivot role to TeamRole enum regardless of cast state. */
    private function roleEnum(mixed $role): ?TeamRole
    {
        if ($role instanceof TeamRole) {
            return $role;
        }

        return TeamRole::tryFrom((string) $role);
    }

    /** Normalize pivot role to its string value. */
    private function roleValue(mixed $role): string
    {
        if ($role instanceof TeamRole) {
            return $role->value;
        }

        return (string) $role;
    }
}
