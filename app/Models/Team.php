<?php

namespace App\Models;

use App\Concerns\GeneratesUniqueTeamSlugs;
use App\Enums\TeamRole;
use App\Enums\TenantPlan;
use App\Models\Drinks\Article;
use App\Models\Drinks\Category;
use App\Models\Drinks\Client;
use App\Models\Drinks\Expense;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\Loss;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\Sale;
use App\Models\Drinks\Supplier;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
}
