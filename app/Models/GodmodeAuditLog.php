<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['super_admin_id', 'target_team_id', 'action', 'entity_type', 'entity_id', 'changes', 'ip_address', 'user_agent'])]
class GodmodeAuditLog extends Model
{
    /**
     * Get the super admin who performed the action.
     *
     * @return BelongsTo<User, $this>
     */
    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    /**
     * Get the team targeted by the action.
     *
     * @return BelongsTo<Team, $this>
     */
    public function targetTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'target_team_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }
}
