<?php

namespace App\Concerns;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to a team (tenant).
 *
 * Automatically scopes all queries to the current user's team
 * and injects team_id on model creation.
 */
trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::addGlobalScope('team', function (Builder $builder) {
            $user = auth()->user();

            if ($user?->current_team_id) {
                $builder->where(
                    $builder->getModel()->getTable().'.team_id',
                    $user->current_team_id
                );
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->team_id)) {
                $model->team_id = auth()->user()?->current_team_id;
            }
        });
    }

    /**
     * Get the team that owns this model.
     *
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
