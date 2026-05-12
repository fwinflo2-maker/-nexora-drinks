<?php

namespace App\Concerns;

use App\Models\Drinks\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            self::logAction($model, 'created');
        });

        static::updated(function (Model $model) {
            // Only log if it's not just the 'validated_at' or 'status' change (we might want separate log for validation)
            self::logAction($model, 'updated');
        });

        static::deleted(function (Model $model) {
            self::logAction($model, 'deleted');
        });
    }

    protected static function logAction(Model $model, string $action)
    {
        if (! auth()->check()) {
            return;
        }

        $team = $model->team ?? auth()->user()->currentTeam;
        if (! $team) {
            return;
        }

        $module = strtolower(class_basename($model));
        $description = self::getActivityDescription($model, $action);

        ActivityLog::log($team, $module, $action, $description, $model);
    }

    protected static function getActivityDescription(Model $model, string $action): string
    {
        $name = $model->name ?? $model->code ?? $model->id;
        $moduleLabel = class_basename($model);

        $actions = [
            'created' => 'a créé',
            'updated' => 'a modifié',
            'deleted' => 'a supprimé',
            'validated' => 'a validé',
        ];

        $actionLabel = $actions[$action] ?? $action;

        return "{$actionLabel} le/la {$moduleLabel} \"{$name}\"";
    }
}
