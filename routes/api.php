<?php

use App\Http\Controllers\Api\CustomProfileController;
use App\Http\Controllers\Api\DashboardAgentController;
use App\Http\Controllers\Api\V1\AutomationRuleController;
use App\Http\Controllers\Api\V1\BusinessJournalController;
use App\Http\Controllers\Api\V1\LedgerController;
use App\Http\Controllers\SuperAdmin\GodmodeController;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Support\Facades\Route;

// ── Routes API v1 ──────────────────────────────────────────────────────────────
Route::prefix('api/v1')
    ->middleware(['api', 'auth', 'verified'])
    ->group(function () {

        // Gestion des profils personnalisés
        Route::prefix('custom-profiles')
            ->controller(CustomProfileController::class)
            ->group(function () {
                Route::get('/', 'index');                           // Liste profils
                Route::post('/', 'store');                          // Créer profil
                Route::get('{customProfile}', 'show');              // Détail profil
                Route::patch('{customProfile}', 'update');          // Modifier profil
                Route::delete('{customProfile}', 'destroy');        // Archiver profil
                Route::post('{customProfile}/duplicate', 'duplicate'); // Dupliquer profil
                Route::get('{customProfile}/users', 'users');       // Utilisateurs assignés
            });

        // Dashboard Agents IA
        Route::prefix('dashboard-agents')
            ->controller(DashboardAgentController::class)
            ->group(function () {
                Route::get('/', 'index');                                    // Lister agents
                Route::get('{dashboardAgent}/conversations', 'conversations'); // Conversations user
                Route::post('{dashboardAgent}/conversations', 'createConversation'); // Créer conversation
                Route::post('conversations/{conversation}/messages', 'sendMessage');  // Envoyer message
            });

        // Business Journal (GAP-01)
        Route::prefix('business-journal')
            ->controller(BusinessJournalController::class)
            ->group(function () {
                Route::get('entries', 'entries');
                Route::get('summary', 'summary');
            });

        // Grand Livre (GAP-01)
        Route::prefix('ledger')
            ->controller(LedgerController::class)
            ->group(function () {
                Route::get('accounts', 'accounts');
                Route::get('trial-balance', 'trialBalance');
            });

        // Règles d'automatisation (GAP-01)
        Route::prefix('automation')
            ->controller(AutomationRuleController::class)
            ->group(function () {
                Route::get('rules', 'index');
                Route::post('rules', 'store');
                Route::patch('rules/{rule}', 'update');
                Route::delete('rules/{rule}', 'destroy');
            });
    });

// ── Routes GODMODE Super Admin ──────────────────────────────────────────────────
Route::prefix('api/v1/godmode')
    ->middleware(['api', 'auth', EnsureSuperAdmin::class])
    ->controller(GodmodeController::class)
    ->group(function () {
        Route::get('dashboard', 'dashboard');                          // Vue complète système
        Route::post('tenants/{team}/impersonate', 'impersonateTenant'); // Prendre contrôle tenant
        Route::post('maintenance', 'toggleMaintenance');               // Activer/Désactiver maintenance
        Route::post('sql/execute', 'executeSql');                      // Exécuter SQL direct
        Route::get('audit-logs', 'auditLogs');                         // Visualiser audit trail
        Route::get('system-logs', 'systemLogs');                       // Visualiser system logs
        Route::post('users/{user}/purge', 'purgeUser');                // Supprimer utilisateur
        Route::post('teams/{team}/reset', 'resetTeam');                // Réinitialiser team
    });
