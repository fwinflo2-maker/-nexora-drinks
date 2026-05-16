<?php

namespace App\Models;

use App\Concerns\GeneratesUniqueTeamSlugs;
use App\Enums\TeamRole;
use App\Enums\TenantPlan;
use App\Models\Drinks\Article;
use App\Models\Drinks\CashDeposit;
use App\Models\Drinks\CashInput;
use App\Models\Drinks\Category;
use App\Models\Drinks\Client;
use App\Models\Drinks\Expense;
use App\Models\Drinks\ExpenseType;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\Loss;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\Payment;
use App\Models\Drinks\PricingTier;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\Sale;
use App\Models\Drinks\StockMovement;
use App\Models\Drinks\StockSnapshot;
use App\Models\Drinks\Supplier;
use App\Models\FnB\Category as FnBCategory;
use App\Models\FnB\MenuItem;
use App\Models\FnB\Order;
use App\Models\FnB\Table;
use App\Models\Hotel\Guest;
use App\Models\Hotel\Reservation;
use App\Models\Hotel\Room;
use App\Models\Hotel\RoomType;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'slug', 'is_personal', 'plan', 'sector', 'settings_json', 'logo_path', 'domain', 'is_active', 'trial_ends_at'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use GeneratesUniqueTeamSlugs, HasFactory, SoftDeletes;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Team $team) {
            if (empty($team->slug)) {
                $team->slug = static::generateUniqueTeamSlug($team->name);
            }
        });

        static::updating(function (Team $team) {
            if ($team->isDirty('name')) {
                $team->slug = static::generateUniqueTeamSlug($team->name, $team->id);
            }
        });
    }

    /**
     * Get the team owner.
     */
    public function owner(): ?Model
    {
        return $this->members()
            ->wherePivot('role', TeamRole::Owner->value)
            ->first();
    }

    /**
     * Get all members of this team.
     *
     * @return BelongsToMany<Model, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id')
            ->using(Membership::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Get all memberships for this team.
     *
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get all invitations for this team.
     *
     * @return HasMany<TeamInvitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * Determine if this team's trial has expired.
     */
    public function isTrialExpired(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isPast();
    }

    /**
     * Determine if this team is on the given plan.
     */
    public function isOnPlan(TenantPlan $plan): bool
    {
        return $this->plan === $plan;
    }

    /**
     * Get a tenant setting value by key.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings_json ?? [];

        return $settings[$key] ?? $default;
    }

    /**
     * Set a tenant setting value by key.
     */
    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings_json ?? [];
        $settings[$key] = $value;

        $this->update(['settings_json' => $settings]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
            'plan' => TenantPlan::class,
            'settings_json' => 'array',
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path
            ? Storage::url($this->logo_path)
            : null;
    }

    /**
     * Get all articles for this team.
     *
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get all drinks categories for this team.
     *
     * @return HasMany<Category, $this>
     */
    public function drinksCategories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get all drinks packagings for this team.
     *
     * @return HasMany<Packaging, $this>
     */
    public function drinksPackagings(): HasMany
    {
        return $this->hasMany(Packaging::class);
    }

    /**
     * Get all drinks sales for this team.
     *
     * @return HasMany<Sale, $this>
     */
    public function drinksSales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all drinks clients for this team.
     *
     * @return HasMany<Client, $this>
     */
    public function drinksClients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get all drinks suppliers for this team.
     *
     * @return HasMany<Supplier, $this>
     */
    public function drinksSuppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * Get all drinks procurements for this team.
     *
     * @return HasMany<Procurement, $this>
     */
    public function drinksProcurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }

    /**
     * Get all drinks expenses for this team.
     *
     * @return HasMany<Expense, $this>
     */
    public function drinksExpenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get all drinks inventories for this team.
     *
     * @return HasMany<Inventory, $this>
     */
    public function drinksInventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get all drinks losses for this team.
     *
     * @return HasMany<Loss, $this>
     */
    public function drinksLosses(): HasMany
    {
        return $this->hasMany(Loss::class);
    }

    /**
     * Get all drinks payments for this team.
     *
     * @return HasMany<Payment, $this>
     */
    public function drinksPayments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all drinks cash inputs for this team.
     *
     * @return HasMany<CashInput, $this>
     */
    public function drinksCashInputs(): HasMany
    {
        return $this->hasMany(CashInput::class);
    }

    /**
     * Get all drinks cash deposits for this team.
     *
     * @return HasMany<CashDeposit, $this>
     */
    public function drinksCashDeposits(): HasMany
    {
        return $this->hasMany(CashDeposit::class);
    }

    /**
     * Get all drinks expense types for this team.
     *
     * @return HasMany<ExpenseType, $this>
     */
    public function drinksExpenseTypes(): HasMany
    {
        return $this->hasMany(ExpenseType::class);
    }

    /**
     * Get all drinks pricing tiers for this team.
     *
     * @return HasMany<PricingTier, $this>
     */
    public function drinksPricingTiers(): HasMany
    {
        return $this->hasMany(PricingTier::class);
    }

    /**
     * Get all drinks stock movements for this team.
     *
     * @return HasMany<StockMovement, $this>
     */
    public function drinksStockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get all drinks stock snapshots for this team.
     *
     * @return HasMany<StockSnapshot, $this>
     */
    public function drinksStockSnapshots(): HasMany
    {
        return $this->hasMany(StockSnapshot::class);
    }

    /**
     * Get all module records for this team.
     *
     * @return HasMany<TeamModule, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(TeamModule::class);
    }

    /**
     * Check whether a given module is active for this team.
     */
    public function hasModule(string $module): bool
    {
        return $this->modules()
            ->where('module', $module)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get all active module slugs for this team.
     *
     * @return Collection<int, TeamModule>
     */
    public function activeModules(): Collection
    {
        return $this->modules()->where('is_active', true)->get();
    }

    // ── Hotel relationships ───────────────────────────────────────────────────

    /** @return HasMany<RoomType, $this> */
    public function hotelRoomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    /** @return HasMany<Room, $this> */
    public function hotelRooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /** @return HasMany<Guest, $this> */
    public function hotelGuests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    /** @return HasMany<Reservation, $this> */
    public function hotelReservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // ── F&B relationships ─────────────────────────────────────────────────────

    /** @return HasMany<FnBCategory, $this> */
    public function fnbCategories(): HasMany
    {
        return $this->hasMany(FnBCategory::class, 'team_id');
    }

    /** @return HasMany<MenuItem, $this> */
    public function fnbMenuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'team_id');
    }

    /** @return HasMany<Table, $this> */
    public function fnbTables(): HasMany
    {
        return $this->hasMany(Table::class, 'team_id');
    }

    /** @return HasMany<Order, $this> */
    public function fnbOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'team_id');
    }

    // ── Module management ─────────────────────────────────────────────────────

    /**
     * Activate a module for this team (idempotent).
     */
    public function activateModule(string $module, ?User $by = null): void
    {
        $this->modules()->updateOrCreate(
            ['module' => $module],
            [
                'is_active' => true,
                'activated_at' => now(),
                'activated_by' => $by?->id,
            ]
        );
    }

    /**
     * Deactivate a module for this team.
     */
    public function deactivateModule(string $module): void
    {
        $this->modules()
            ->where('module', $module)
            ->update(['is_active' => false]);
    }
}
