<?php

namespace App\Models\Drinks;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'drinks_activity_logs';

    protected $fillable = [
        'team_id', 'user_id', 'module', 'action',
        'description', 'model_type', 'model_id', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function log(Team $team, string $module, string $action, string $description, ?Model $model = null, array $metadata = []): self
    {
        return self::create([
            'team_id' => $team->id,
            'user_id' => auth()->id(),
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'metadata' => $metadata,
        ]);
    }
}
