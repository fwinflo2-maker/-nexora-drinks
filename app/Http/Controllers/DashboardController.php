<?php

namespace App\Http\Controllers;

use App\Enums\WarehouseType;
use App\Models\Category;
use App\Models\GodmodeAuditLog;
use App\Models\Team;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Dashboard pour les équipes (Admin entreprise, Gérant, etc.)
     */
    public function teamDashboard(Request $request, ?Team $current_team = null)
    {
        $team = $current_team;

        if ($request->user() && $request->user()->nexora_role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        if (! $team) {
            return redirect()->route('home');
        }

        return redirect()->route('drinks.dashboard', ['current_team' => $team->slug]);
    }

    /**
     * Dashboard pour le Super Admin (Vue globale)
     */
    public function superAdminDashboard()
    {
        $totalCompanies = Team::count();
        $activeCompanies = Team::where('is_active', true)->count();
        $pendingCompanies = Team::where('is_active', false)->count();
        $totalUsers = User::count();

        $tenants = Team::withCount('members')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name ?? 'Sans nom',
                    'type' => $team->sector ?? 'Boissons',
                    'plan' => $team->plan?->label() ?? 'Starter',
                    'status' => $team->is_active ? 'active' : ($team->members()->whereNotNull('blocked_at')->exists() ? 'suspendu' : 'pending'),
                    'users_count' => $team->members_count ?? 0,
                    'users_limit' => $team->plan?->value === 'pro' ? 20 : ($team->plan?->value === 'enterprise' ? 50 : 5),
                    'ca_month_xaf' => 0,
                    'orders_count' => 0,
                    'deliveries_count' => 0,
                    'joined_at' => $team->created_at ? $team->created_at->translatedFormat('M Y') : 'Inconnu',
                    'modules_enabled' => ['drinks'],
                ];
            })->values()->toArray();

        return Inertia::render('super-admin/dashboard', [
            'userName' => auth()->user()->name,
            'networkKpis' => [
                'total_tenants' => $totalCompanies,
                'active_tenants' => $activeCompanies,
                'suspended_tenants' => $pendingCompanies,
                'total_users' => $totalUsers,
                'total_transactions' => 0,
                'mrr_xaf' => 0,
            ],
            'tenants' => $tenants,
            'users' => User::with(['teams:id,name,slug'])->orderBy('created_at', 'desc')->get()->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'nexora_role' => $u->nexora_role,
                'email_verified_at' => $u->email_verified_at?->toDateTimeString(),
                'created_at' => $u->created_at->toDateTimeString(),
                'teams' => $u->teams->map(fn ($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'role' => $t->pivot->role ?? null,
                ]),
            ]),
            'systemHealth' => [
                'database' => $this->checkDatabaseHealth(),
                'maintenance_enabled' => cache('godmode_maintenance_enabled', false),
                'pending_jobs' => DB::table('jobs')->count(),
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ],
            'recentAuditLogs' => GodmodeAuditLog::with(['superAdmin:id,name', 'targetTeam:id,name'])
                ->latest()
                ->limit(50)
                ->get()
                ->map(fn ($l) => [
                    'id' => $l->id,
                    'action' => $l->action,
                    'super_admin' => $l->superAdmin?->name,
                    'target_team' => $l->targetTeam?->name,
                    'ip_address' => $l->ip_address,
                    'changes' => $l->changes,
                    'created_at' => $l->created_at->toDateTimeString(),
                ]),
        ]);
    }

    private function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();

            return ['status' => 'healthy'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    /**
     * Overview - Dashboard initial (role-agnostic)
     * Redirige systématiquement vers le dashboard Drinks
     */
    public function overview(Request $request, Team $current_team)
    {
        return redirect()->route('drinks.dashboard', ['current_team' => $current_team->slug]);
    }

    /**
     * Gestion des Profils (Admin only)
     */
    public function profiles(Request $request, Team $current_team)
    {
        if (auth()->user()->teamRole($current_team)?->value !== 'admin') {
            return redirect()->route('dashboard.overview', $current_team->slug)
                ->withErrors(['unauthorized' => 'Réservé aux administrateurs']);
        }

        $teamMembers = $current_team->members()
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

        return Inertia::render('dashboard', [
            'section' => 'profiles',
            'teamName' => $current_team->name,
            'teamMembers' => $teamMembers,
            'categories' => Category::where('team_id', $current_team->id)->get(['id', 'name']),
        ]);
    }

    /**
     * Activer une entreprise (Tenant)
     */
    public function activateTenant(Team $team)
    {
        DB::transaction(function () use ($team) {
            $team->update(['is_active' => true]);
            
            // Débloquer tous les membres de l'entreprise
            $team->members()->update(['blocked_at' => null]);

            // Configuration Boissons par défaut
            $team->update(['sector' => 'Boissons']);

            // Entrepôt par défaut
            Warehouse::firstOrCreate([
                'team_id' => $team->id,
                'name' => 'Dépôt Principal',
            ], [
                'type' => WarehouseType::Main,
                'is_active' => true,
            ]);
        });

        return back()->with('success', "Le tenant {$team->name} a été activé pour le secteur Boissons.");
    }

    public function suspendTenant(Team $team)
    {
        DB::transaction(function () use ($team) {
            $team->update(['is_active' => false]);
            
            // Suspendre tous les membres de l'entreprise (sauf les super admins)
            $team->members()
                ->where('nexora_role', '!=', 'super_admin')
                ->update(['blocked_at' => now()]);
        });

        return back()->with('success', "Le tenant {$team->name} et tous ses comptes ont été suspendus.");
    }

    public function deleteTenant(Team $team)
    {
        DB::transaction(function () use ($team) {
            // Soft delete de l'entreprise
            $team->delete();
        });

        return back()->with('success', "L'entreprise {$team->name} a été supprimée (archivée).");
    }

    public function loginAsCompany(Team $team)
    {
        $admin = $team->members()->wherePivot('role', 'admin')->first() ?? $team->members()->first();
        if ($admin) {
            $superAdminId = auth()->id();
            auth()->login($admin);
            session(['impersonator_id' => $superAdminId]);

            return redirect()->route('drinks.dashboard', $team->slug);
        }

        return back()->withErrors(['error' => 'Aucun utilisateur trouvé.']);
    }

    public function stopImpersonating()
    {
        $superAdminId = session('impersonator_id');
        if ($superAdminId) {
            $superAdmin = User::find($superAdminId);
            if ($superAdmin) {
                auth()->login($superAdmin);
                session()->forget('impersonator_id');

                return redirect()->route('super-admin.dashboard');
            }
        }

        return redirect()->route('home');
    }

    public function toggleMaintenance()
    {
        $enabled = ! cache('godmode_maintenance_enabled', false);
        cache(['godmode_maintenance_enabled' => $enabled], now()->addHours(24));

        return back()->with('success', 'Maintenance '.($enabled ? 'activée' : 'désactivée').'.');
    }

    public function broadcastMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        return back()->with('success', 'Message diffusé (simulation).');
    }
}
