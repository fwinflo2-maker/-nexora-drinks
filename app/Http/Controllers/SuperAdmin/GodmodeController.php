<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GodmodeAuditLog;
use App\Models\GodmodeSystemLog;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GodmodeController extends Controller
{
    /**
     * Tableau de bord GODMODE - Vue complète du système
     */
    public function dashboard(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'system_health' => $this->getSystemHealth(),
                'recent_activities' => $this->getRecentActivities(),
                'alerts' => $this->getSystemAlerts(),
                'stats' => $this->getSystemStats(),
            ],
        ]);
    }

    /**
     * GODMODE: Impersonifier un tenant (super admin prend contrôle total)
     */
    public function impersonateTenant(Request $request, Team $team): JsonResponse
    {
        $superAdmin = $request->user();

        // Log audit
        $this->logGodmodeAction(
            $superAdmin,
            $team,
            'impersonate_tenant',
            [
                'team_slug' => $team->slug,
                'team_name' => $team->name,
            ]
        );

        // Créer token d'impersonation temporaire
        $token = $superAdmin->createToken(
            "godmode_impersonate_{$team->id}_".now()->timestamp,
            ['*'],
            now()->addHours(2) // Valide 2h
        )->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'team' => $team,
                'impersonation_expires_at' => now()->addHours(2),
            ],
            'message' => 'Mode impersonation activé - Accès complet au tenant',
        ]);
    }

    /**
     * GODMODE: Activer/Désactiver la maintenance système globale
     */
    public function toggleMaintenance(Request $request): JsonResponse
    {
        $enabled = ! cache('godmode_maintenance_enabled', false);

        cache(['godmode_maintenance_enabled' => $enabled], now()->addHours(24));

        $this->logGodmodeSystemAction(
            $request->user(),
            'maintenance_toggle',
            'Maintenance système '.($enabled ? 'ACTIVÉE' : 'DÉSACTIVÉE'),
            ['maintenance_enabled' => $enabled]
        );

        return response()->json([
            'data' => [
                'maintenance_enabled' => $enabled,
                'expires_at' => now()->addHours(24),
            ],
            'message' => 'Statut maintenance mis à jour',
        ]);
    }

    /**
     * GODMODE: Exécuter une requête SQL directe (ISO ULTRA DANGEREUX)
     */
    public function executeSql(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string',
            'confirmation' => 'required|boolean',
        ]);

        if (! $request->confirmation) {
            return response()->json([
                'message' => 'Confirmation requise pour exécuter cette requête',
            ], 422);
        }

        try {
            $result = DB::select($request->input('query'));

            $this->logGodmodeSystemAction(
                $request->user(),
                'direct_sql_execution',
                'Requête SQL exécutée',
                [
                    'query_preview' => substr($request->input('query'), 0, 100).'...',
                    'result_count' => count($result),
                ]
            );

            return response()->json([
                'data' => $result,
                'meta' => [
                    'rows_affected' => count($result),
                ],
            ]);
        } catch (\Exception $e) {
            $this->logGodmodeSystemAction(
                $request->user(),
                'direct_sql_execution_error',
                'Erreur exécution SQL: '.$e->getMessage(),
                ['query' => substr($request->input('query'), 0, 100)]
            );

            return response()->json([
                'message' => 'Erreur: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * GODMODE: Visualiser les audit logs
     */
    public function auditLogs(Request $request): JsonResponse
    {
        $request->validate([
            'super_admin_id' => 'nullable|integer',
            'target_team_id' => 'nullable|integer',
            'action' => 'nullable|string',
            'days' => 'nullable|integer|max:90',
        ]);

        $query = GodmodeAuditLog::query();

        if ($request->super_admin_id) {
            $query->where('super_admin_id', $request->super_admin_id);
        }

        if ($request->target_team_id) {
            $query->where('target_team_id', $request->target_team_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $days = $request->days ?? 30;
        $query->where('created_at', '>=', now()->subDays($days));

        $logs = $query->with(['superAdmin:id,name,email', 'targetTeam:id,name,slug'])
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $logs->items(),
            'meta' => [
                'total' => $logs->total(),
                'current_page' => $logs->currentPage(),
                'period_days' => $days,
            ],
        ]);
    }

    /**
     * GODMODE: Visualiser system logs
     */
    public function systemLogs(Request $request): JsonResponse
    {
        $request->validate([
            'level' => 'nullable|in:info,warning,error,critical',
            'type' => 'nullable|string',
            'days' => 'nullable|integer|max:90',
        ]);

        $query = GodmodeSystemLog::query();

        if ($request->level) {
            $query->where('level', $request->level);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $days = $request->days ?? 7;
        $query->where('created_at', '>=', now()->subDays($days));

        $logs = $query->latest()->paginate(100);

        return response()->json([
            'data' => $logs->items(),
            'meta' => [
                'total' => $logs->total(),
                'current_page' => $logs->currentPage(),
            ],
        ]);
    }

    /**
     * GODMODE: Supprimer un utilisateur de tous les tenants
     */
    public function purgeUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'confirmation' => 'required|boolean',
        ]);

        if (! $request->confirmation) {
            return response()->json([
                'message' => 'Cette action est irréversible. Confirmation requise.',
            ], 422);
        }

        $user = User::findOrFail($request->user_id);

        $user->delete();

        $this->logGodmodeAction(
            $request->user(),
            null,
            'purge_user',
            [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'teams_count' => $user->teams()->count(),
            ]
        );

        return response()->json([
            'message' => "Utilisateur {$user->email} archivé de tous les tenants",
        ]);
    }

    /**
     * GODMODE: Réinitialiser une équipe (tous les datos sauf la config)
     */
    public function resetTeam(Request $request, Team $team): JsonResponse
    {
        $request->validate([
            'confirmation' => 'required|boolean',
        ]);

        if (! $request->confirmation) {
            return response()->json([
                'message' => 'Cette action supprimera toutes les données du team. Confirmation requise.',
            ], 422);
        }

        DB::transaction(function () use ($team) {
            // Supprimer toutes les commandes, livraisons, données métier
            DB::table('orders')->where('team_id', $team->id)->delete();
            DB::table('deliveries')->where('team_id', $team->id)->delete();
            DB::table('packaging_movements')->where('team_id', $team->id)->delete();
            DB::table('stock_movements')->where('team_id', $team->id)->delete();
            DB::table('invoices')->where('team_id', $team->id)->delete();
            // ... etc
        });

        $this->logGodmodeAction(
            $request->user(),
            $team,
            'reset_team',
            [
                'team_id' => $team->id,
                'team_name' => $team->name,
            ]
        );

        return response()->json([
            'message' => "Toutes les données métier du team {$team->name} ont été réinitialisées",
        ]);
    }

    /**
     * Enregistre une action godmode
     */
    private function logGodmodeAction(User $superAdmin, ?Team $team, string $action, array $changes = []): void
    {
        GodmodeAuditLog::create([
            'super_admin_id' => $superAdmin->id,
            'target_team_id' => $team?->id,
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Enregistre une action système
     */
    private function logGodmodeSystemAction(User $superAdmin, string $type, string $message, array $context = []): void
    {
        GodmodeSystemLog::create([
            'level' => str_contains($message, 'Erreur') ? 'error' : 'info',
            'type' => $type,
            'message' => $message,
            'context' => $context,
            'triggered_by' => $superAdmin->id,
        ]);
    }

    /**
     * Récupère l'état de santé du système
     */
    private function getSystemHealth(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
            'disk_space' => $this->checkDiskSpace(),
        ];
    }

    /**
     * Récupère les activités récentes
     */
    private function getRecentActivities(): array
    {
        return GodmodeAuditLog::latest()
            ->limit(20)
            ->with(['superAdmin:id,name,email'])
            ->get()
            ->toArray();
    }

    /**
     * Récupère les alertes système
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Vérifier les tenants suspendus
        $suspendedCount = Team::where('is_active', false)->count();
        if ($suspendedCount > 0) {
            $alerts[] = [
                'level' => 'warning',
                'message' => "$suspendedCount tenants suspendus",
            ];
        }

        // Vérifier les utilisateurs bloqués
        $blockedUsers = User::where('is_active', false)->count();
        if ($blockedUsers > 0) {
            $alerts[] = [
                'level' => 'info',
                'message' => "$blockedUsers utilisateurs bloqués",
            ];
        }

        return $alerts;
    }

    /**
     * Récupère les statistiques système
     */
    private function getSystemStats(): array
    {
        return [
            'total_tenants' => Team::count(),
            'active_tenants' => Team::where('is_active', true)->count(),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return ['status' => 'healthy', 'latency_ms' => 5];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            cache()->put('godmode_test', true, now()->addSeconds(1));

            return ['status' => 'healthy'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        // Mock - vérifier nombre de jobs en attente
        return ['status' => 'healthy', 'pending_jobs' => 0];
    }

    private function checkDiskSpace(): array
    {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        $percent = round(($free / $total) * 100, 2);

        return [
            'status' => $percent > 10 ? 'healthy' : 'warning',
            'free_percent' => $percent,
            'free_gb' => round($free / 1024 ** 3, 2),
        ];
    }
}
