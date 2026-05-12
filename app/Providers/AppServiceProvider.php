<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Drinks\Article;
use App\Models\Drinks\CashDeposit;
use App\Models\Drinks\CashInput;
use App\Models\Drinks\Category;
use App\Models\Drinks\Client;
use App\Models\Drinks\Expense;
use App\Models\Drinks\ExpenseType;
use App\Models\Drinks\Family;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\Loss;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\Payment;
use App\Models\Drinks\PricingTier;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\Sale;
use App\Models\Drinks\Setting;
use App\Models\Drinks\StockMovement;
use App\Models\Drinks\StockSnapshot;
use App\Models\Drinks\Supplier;
use App\Models\FoodLot;
use App\Policies\Drinks\ArticlePolicy;
use App\Policies\Drinks\CashDepositPolicy;
use App\Policies\Drinks\CashInputPolicy;
use App\Policies\Drinks\CategoryPolicy;
use App\Policies\Drinks\ClientPolicy;
use App\Policies\Drinks\ExpensePolicy;
use App\Policies\Drinks\ExpenseTypePolicy;
use App\Policies\Drinks\FamilyPolicy;
use App\Policies\Drinks\InventoryPolicy;
use App\Policies\Drinks\LossPolicy;
use App\Policies\Drinks\PackagingPolicy;
use App\Policies\Drinks\PaymentPolicy;
use App\Policies\Drinks\PricingTierPolicy;
use App\Policies\Drinks\ProcurementPolicy;
use App\Policies\Drinks\SalePolicy;
use App\Policies\Drinks\SettingPolicy;
use App\Policies\Drinks\StockMovementPolicy;
use App\Policies\Drinks\StockSnapshotPolicy;
use App\Policies\Drinks\SupplierPolicy;
use App\Policies\FoodLotPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePolicies();
        $this->configureDefaults();
    }

    /**
     * Register all model policies.
     */
    protected function configurePolicies(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->nexora_role === 'super_admin' ? true : null;
        });

        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(Family::class, FamilyPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(PricingTier::class, PricingTierPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Packaging::class, PackagingPolicy::class);
        Gate::policy(Procurement::class, ProcurementPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Inventory::class, InventoryPolicy::class);
        Gate::policy(Loss::class, LossPolicy::class);
        Gate::policy(ExpenseType::class, ExpenseTypePolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(CashInput::class, CashInputPolicy::class);
        Gate::policy(CashDeposit::class, CashDepositPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(StockMovement::class, StockMovementPolicy::class);
        Gate::policy(StockSnapshot::class, StockSnapshotPolicy::class);
        Gate::policy(FoodLot::class, FoodLotPolicy::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
