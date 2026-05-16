<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;

// nexora_role intentionally excluded from mass-assignment — use setNexoraRole()
#[Fillable(['name', 'email', 'avatar', 'password', 'current_team_id', 'blocked_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'blocked_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    protected $appends = ['avatar_url'];

    public function isBlocked(): bool
    {
        return $this->blocked_at !== null;
    }

    public function setNexoraRole(string $role): void
    {
        $previous = $this->nexora_role;
        $this->forceFill(['nexora_role' => $role])->save();

        DB::table('godmode_audit_logs')->insert([
            'super_admin_id' => auth()->id() ?? $this->id,
            'target_team_id' => null,
            'action' => 'set_nexora_role',
            'changes' => json_encode(['from' => $previous, 'to' => $role, 'user_id' => $this->id]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }
}
