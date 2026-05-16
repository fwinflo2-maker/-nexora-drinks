<?php

use App\Http\Controllers\Auth\NexaChatController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\ConsignationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Drinks;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\TourneeController;
use App\Http\Middleware\CheckModuleAccess;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('otp.send')->middleware('throttle:5,1');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify')->middleware('throttle:10,1');
Route::post('/nexa-chat', [NexaChatController::class, 'handleChat'])->name('nexa.chat')->middleware('throttle:30,1');

// ── Route de redirection après connexion (Fortify utilise /dashboard) ────────
Route::middleware(['auth'])->get('/dashboard', function (Request $request) {
    $user = $request->user();

    if ($user->nexora_role === 'super_admin') {
        return redirect()->route('super-admin.dashboard');
    }

    $team = $user->teams()->first();
    if ($team) {
        return redirect()->route('dashboard.overview', ['current_team' => $team->slug ?? $team->id]);
    }

    return redirect()->route('home');
})->name('dashboard');

// ── Routes Super Admin ───────────────────────────────────────────────────────
Route::prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        // Page de login super admin (accessible sans authentification)
        // On vide l'URL "intended" en session pour éviter toute redirection parasite
        Route::get('login', function (Request $request) {
            $request->session()->forget('url.intended');

            return Inertia::render('super-admin/login', [
                'status' => $request->session()->get('status'),
            ]);
        })->name('login');

        Route::middleware(['auth'])->post('stop-impersonating', [DashboardController::class, 'stopImpersonating'])->name('stop-impersonating');

        // Dashboard et actions protégés
        Route::middleware(['auth', EnsureSuperAdmin::class])->group(function () {
            Route::get('dashboard', [DashboardController::class, 'superAdminDashboard'])->name('dashboard');
            Route::post('tenants/{team:id}/activate', [DashboardController::class, 'activateTenant'])->name('tenants.activate');
            Route::post('tenants/{team:id}/suspend', [DashboardController::class, 'suspendTenant'])->name('tenants.suspend');
            Route::delete('tenants/{team:id}/delete', [DashboardController::class, 'deleteTenant'])->name('tenants.delete');
            Route::post('tenants/{team:id}/impersonate', [DashboardController::class, 'loginAsCompany'])->name('tenants.impersonate');

            // Paramètres globaux et système
            Route::post('system/maintenance', [DashboardController::class, 'toggleMaintenance'])->name('system.maintenance');
            Route::post('settings/broadcast-message', [DashboardController::class, 'broadcastMessage'])->name('settings.broadcast');

            // ── Modules ──────────────────────────────────────────────────────────────
            Route::get('tenants/{team:id}/modules', [ModuleController::class, 'index'])->name('tenants.modules.index');
            Route::post('tenants/{team:id}/modules/{module}/activate', [ModuleController::class, 'activate'])->name('tenants.modules.activate');
            Route::post('tenants/{team:id}/modules/{module}/deactivate', [ModuleController::class, 'deactivate'])->name('tenants.modules.deactivate');
        });
    });

// ── Routes équipes (utilisateurs normaux) ────────────────────────────────────
Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        // Dashboard principal — redirige selon le secteur/rôle
        Route::get('dashboard', [DashboardController::class, 'overview'])->name('dashboard.overview');

        // Dashboard — commercial (ventes & clients)
        Route::get('dashboard/commercial', [DashboardController::class, 'commercial'])->name('dashboard.commercial');

        // Dashboard — profils & agent IA
        Route::get('dashboard/profiles', [DashboardController::class, 'profiles'])->name('dashboard.profiles');
        Route::get('dashboard/agent', [DashboardController::class, 'agent'])->name('dashboard.agent');

        // Dashboard — Hotel + F&B bridge (Mode 3)
        Route::get('dashboard/hotel-fnb', [DashboardController::class, 'hotelFnB'])->name('dashboard.hotel-fnb');

        // ── Consignations ─────────────────────────────────────────────────────────────
        Route::prefix('consignations')->name('consignations.')->group(function () {
            Route::get('/', [ConsignationController::class, 'index'])->name('index');
            Route::post('/', [ConsignationController::class, 'store'])->name('store');
            Route::get('{client}', [ConsignationController::class, 'show'])->name('show');
            Route::post('{client}/mouvements', [ConsignationController::class, 'storeMovement'])->name('movements.store');
        });

        // ── Equipe ─────────────────────────────────────────────────────────────────
        Route::prefix('equipe')->name('equipe.')->group(function () {
            Route::get('/', [EquipeController::class, 'index'])->name('index');
            Route::post('/', [EquipeController::class, 'store'])->name('store');
            Route::patch('{membership}', [EquipeController::class, 'update'])->name('update');
            Route::delete('{membership}', [EquipeController::class, 'destroy'])->name('destroy');
        });

        // ── Factures ───────────────────────────────────────────────────────────────
        Route::prefix('factures')->name('factures.')->group(function () {
            Route::get('/', [FactureController::class, 'index'])->name('index');
            Route::post('/', [FactureController::class, 'store'])->name('store');
            Route::get('{invoice}', [FactureController::class, 'show'])->name('show');
            Route::patch('{invoice}', [FactureController::class, 'update'])->name('update');
            Route::delete('{invoice}', [FactureController::class, 'destroy'])->name('destroy');
            Route::post('{invoice}/paiements', [FactureController::class, 'storePaiement'])->name('paiements.store');
        });

        // ── Finances ───────────────────────────────────────────────────────────────
        Route::prefix('finances')->name('finances.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('rapports', [FinanceController::class, 'rapports'])->name('rapports');
            Route::post('depenses', [FinanceController::class, 'storeDepense'])->name('depenses.store');
            Route::delete('depenses/{expense}', [FinanceController::class, 'destroyDepense'])->name('depenses.destroy');
        });

        // ── Stocks ─────────────────────────────────────────────────────────────────
        Route::prefix('stocks')->name('stocks.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('mouvements', [StockController::class, 'mouvements'])->name('mouvements');
            Route::post('mouvements', [StockController::class, 'storeMovement'])->name('mouvements.store');
        });

        // ── Tournées ───────────────────────────────────────────────────────────────
        Route::prefix('tournees')->name('tournees.')->group(function () {
            Route::get('/', [TourneeController::class, 'index'])->name('index');
            Route::post('/', [TourneeController::class, 'store'])->name('store');
            Route::get('{deliveryRoute}', [TourneeController::class, 'show'])->name('show');
            Route::patch('{deliveryRoute}', [TourneeController::class, 'update'])->name('update');
            Route::delete('{deliveryRoute}', [TourneeController::class, 'destroy'])->name('destroy');
            Route::get('{deliveryRoute}/livraisons/{delivery}', [TourneeController::class, 'showDelivery'])->name('deliveries.show');
            Route::patch('{deliveryRoute}/livraisons/{delivery}', [TourneeController::class, 'updateDelivery'])->name('deliveries.update');
            Route::post('{deliveryRoute}/collectes', [TourneeController::class, 'storeCollection'])->name('collections.store');
        });

        // ── Drinks ────────────────────────────────────────────────────────────────────
        Route::prefix('drinks')->name('drinks.')->middleware(CheckModuleAccess::class.':drinks')->group(function () {
            Route::resource('articles', Drinks\ArticleController::class)->parameters(['articles' => 'article']);
            Route::resource('categories', Drinks\CategoryController::class)->except(['show'])->parameters(['categories' => 'category']);
            Route::resource('pricing-tiers', Drinks\PricingTierController::class)->except(['show'])->parameters(['pricing-tiers' => 'pricingTier']);
            Route::post('clients/quick-store', [Drinks\ClientController::class, 'quickStore'])->name('clients.quick-store');
            Route::resource('clients', Drinks\ClientController::class)->parameters(['clients' => 'client']);
            Route::resource('suppliers', Drinks\SupplierController::class)->except(['show'])->parameters(['suppliers' => 'supplier']);
            Route::resource('packagings', Drinks\PackagingController::class)->except(['show'])->parameters(['packagings' => 'packaging']);
            Route::resource('settings', Drinks\SettingController::class)->only(['update']);
            Route::post('settings/update-branding', [Drinks\SettingController::class, 'updateBranding'])->name('settings.update-branding');
            Route::resource('procurements', Drinks\ProcurementController::class);
            Route::post('procurements/{procurement}/validate', [Drinks\ProcurementController::class, 'validateProcurement'])->name('procurements.validate');
            Route::post('procurements/{procurement}/cancel-validation', [Drinks\ProcurementController::class, 'cancelValidation'])->name('procurements.cancel-validation');
            Route::resource('sales', Drinks\SaleController::class);
            Route::post('sales/{sale}/validate', [Drinks\SaleController::class, 'validateSale'])->name('sales.validate');
            Route::post('sales/{sale}/cancel-validation', [Drinks\SaleController::class, 'cancelValidation'])->name('sales.cancel-validation');
            Route::post('sale-packaging-lines/{salePackagingLine}/deconsign', [Drinks\DeconsignmentController::class, 'process'])->name('sale-packaging-lines.deconsign');
            Route::resource('inventories', Drinks\InventoryController::class);
            Route::post('inventories/{inventory}/validate', [Drinks\InventoryController::class, 'validateInventory'])->name('inventories.validate');
            Route::post('inventories/{inventory}/cancel-validation', [Drinks\InventoryController::class, 'cancelValidation'])->name('inventories.cancel-validation');
            Route::resource('losses', Drinks\LossController::class);
            Route::post('losses/{loss}/validate', [Drinks\LossController::class, 'validateLoss'])->name('losses.validate');
            Route::post('losses/{loss}/cancel-validation', [Drinks\LossController::class, 'cancelValidation'])->name('losses.cancel-validation');
            Route::resource('stock-movements', Drinks\StockMovementController::class)->only(['index', 'show']);
            Route::get('stock-snapshots', [Drinks\StockSnapshotController::class, 'index'])->name('stock-snapshots.index');
            Route::post('stock-snapshots', [Drinks\StockSnapshotController::class, 'store'])->name('stock-snapshots.store');
            Route::get('stock-snapshots/{date}', [Drinks\StockSnapshotController::class, 'show'])->name('stock-snapshots.show');
            // ── Lot 8 — Finance ──────────────────────────────────────────────────────
            Route::resource('expense-types', Drinks\ExpenseTypeController::class)->except(['show'])->parameters(['expense-types' => 'expenseType']);
            Route::resource('expenses', Drinks\ExpenseController::class);
            Route::post('expenses/{expense}/validate', [Drinks\ExpenseController::class, 'validateExpense'])->name('expenses.validate');
            Route::post('expenses/{expense}/cancel-validation', [Drinks\ExpenseController::class, 'cancelValidation'])->name('expenses.cancel-validation');
            Route::resource('cash-inputs', Drinks\CashInputController::class)->only(['index', 'show', 'create', 'store', 'destroy'])->parameters(['cash-inputs' => 'cashInput']);
            Route::post('cash-inputs/{cashInput}/validate', [Drinks\CashInputController::class, 'validateCashInput'])->name('cash-inputs.validate');
            Route::post('cash-inputs/{cashInput}/cancel-validation', [Drinks\CashInputController::class, 'cancelValidation'])->name('cash-inputs.cancel-validation');
            Route::resource('cash-deposits', Drinks\CashDepositController::class)->only(['index', 'show', 'create', 'store', 'destroy'])->parameters(['cash-deposits' => 'cashDeposit']);
            Route::post('cash-deposits/{cashDeposit}/validate', [Drinks\CashDepositController::class, 'validateDeposit'])->name('cash-deposits.validate');
            Route::post('cash-deposits/{cashDeposit}/cancel-validation', [Drinks\CashDepositController::class, 'cancelValidation'])->name('cash-deposits.cancel-validation');
            Route::resource('payments', Drinks\PaymentController::class)->except(['edit', 'update']);
            Route::post('payments/{payment}/validate', [Drinks\PaymentController::class, 'validatePayment'])->name('payments.validate');
            Route::post('payments/{payment}/cancel-validation', [Drinks\PaymentController::class, 'cancelValidation'])->name('payments.cancel-validation');
            Route::post('payments/{payment}/allocate', [Drinks\PaymentController::class, 'allocate'])->name('payments.allocate');
            // ── Lot 9 — Reports & PDF ────────────────────────────────────────────────
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('brouillard', [Drinks\ReportController::class, 'brouillard'])->name('brouillard');
                Route::get('brouillard/pdf', [Drinks\ReportController::class, 'brouillardPdf'])->name('brouillard.pdf');
                Route::get('sales', [Drinks\ReportController::class, 'salesReport'])->name('sales');
                Route::get('sales/pdf', [Drinks\ReportController::class, 'salesReportPdf'])->name('sales.pdf');
                Route::get('stock', [Drinks\ReportController::class, 'stockReport'])->name('stock');
                Route::get('stock/pdf', [Drinks\ReportController::class, 'stockReportPdf'])->name('stock.pdf');
                Route::get('clients', [Drinks\ReportController::class, 'clientReport'])->name('clients');
                Route::get('clients/pdf', [Drinks\ReportController::class, 'clientReportPdf'])->name('clients.pdf');
                Route::get('roadmap/pdf', [Drinks\ReportController::class, 'roadmapPdf'])->name('roadmap.pdf');
                Route::get('roadmap', [Drinks\ReportController::class, 'roadmap'])->name('roadmap');
            });
            // PDF sur ressources existantes
            Route::get('procurements/{procurement}/pdf', [Drinks\ProcurementController::class, 'pdf'])->name('procurements.pdf');
            Route::get('sales/{sale}/pdf', [Drinks\SaleController::class, 'pdf'])->name('sales.pdf');
            Route::get('payments/{payment}/pdf', [Drinks\PaymentController::class, 'pdf'])->name('payments.pdf');
            Route::get('cash-deposits/{cashDeposit}/pdf', [Drinks\CashDepositController::class, 'pdf'])->name('cash-deposits.pdf');
            // ── Dashboard Drinks ─────────────────────────────────────────────────────
            Route::get('dashboard', [Drinks\DashboardController::class, 'index'])->name('dashboard');
            Route::get('logs', [Drinks\DashboardController::class, 'logs'])->name('logs');
            Route::get('settings', [Drinks\DashboardController::class, 'settings'])->name('settings.index');
            // ── Membres (gestion d'équipe)
            Route::get('membres', [Drinks\MembresController::class, 'index'])->name('membres.index');
            Route::post('membres/store', [Drinks\MembresController::class, 'store'])->name('membres.store');
            Route::patch('membres/{user}/password', [Drinks\MembresController::class, 'updatePassword'])->name('membres.update-password');
            Route::patch('membres/{user}/block', [Drinks\MembresController::class, 'block'])->name('membres.block');
            Route::patch('membres/{user}/unblock', [Drinks\MembresController::class, 'unblock'])->name('membres.unblock');
            Route::patch('membres/{user}/profile', [Drinks\MembresController::class, 'updateProfile'])->name('membres.update-profile');
            Route::patch('membres/{user}/role', [Drinks\MembresController::class, 'updateRole'])->name('membres.update-role');
            Route::post('membres/{user}/remove', [Drinks\MembresController::class, 'remove'])->name('membres.remove');
            // ── Agent IA ──────────────────────────────────────────────────────────────
            Route::post('agent/chat', [Drinks\AgentChatController::class, 'chat'])->name('agent.chat');
        });
    });

// ── Autres routes authentifiées ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::get('/pending-approval', function (Request $request) {
        $user = $request->user();
        $team = $user?->currentTeam;

        // On considère suspendu si le compte est bloqué ou si l'équipe est inactive
        // (Les nouveaux comptes sont aussi inactifs, mais on peut différencier par la date de création si besoin)
        $isSuspended = ($user && $user->blocked_at) || ($team && ! $team->is_active && $team->created_at->diffInMinutes(now()) > 5);

        return Inertia::render('auth/pending-approval', [
            'isSuspended' => $isSuspended,
        ]);
    })->name('pending-approval');
});

// TODO: create resources/js/pages/Onboarding/NexoraOnboarding.tsx before re-enabling
// Route::inertia('/onboarding', 'Onboarding/NexoraOnboarding')->name('onboarding');

Route::inertia('/docs', 'docs')->name('docs');

require __DIR__.'/settings.php';
