<?php

namespace App\Concerns;

trait HasCodeGeneration
{
    /**
     * Boot the trait.
     */
    protected static function bootHasCodeGeneration(): void
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = $model->generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique code for the model.
     */
    public function generateUniqueCode(): string
    {
        $prefix = $this->getCodePrefix();
        $date = now()->format('Ymd');

        // Count total for team to get sequence
        // Using withoutGlobalScopes to ensure we don't miss any due to soft deletes or other scopes
        $count = static::withoutGlobalScopes()
            ->where('team_id', $this->team_id)
            ->count();

        $sequence = $count + 1;

        return "{$prefix}-{$date}-".str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the code prefix for the model.
     */
    abstract protected function getCodePrefix(): string;
}
