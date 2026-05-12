<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomProfile extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'description',
        'permissions',
        'sector',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'custom_profile_id');
    }

    /**
     * Vérifie si le profil a une permission pour une action sur un module
     */
    public function hasPermission(string $module, string $action = 'read'): bool
    {
        $permissions = $this->permissions ?? [];

        if (! isset($permissions[$module])) {
            return false;
        }

        return in_array($action, $permissions[$module]);
    }

    /**
     * Récupère tous les modules configurés pour ce profil
     */
    public function getModules(): array
    {
        return array_keys($this->permissions ?? []);
    }

    /**
     * Crée un clone du profil pour un autre team/tenant
     */
    public function duplicate(Team $team, User $creator): static
    {
        return static::create([
            'team_id' => $team->id,
            'name' => $this->name.' (Copie)',
            'description' => $this->description,
            'permissions' => $this->permissions,
            'sector' => $this->sector,
            'is_active' => true,
            'created_by' => $creator->id,
        ]);
    }
}
