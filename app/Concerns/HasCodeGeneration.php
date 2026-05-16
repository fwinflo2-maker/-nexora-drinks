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
            $field = method_exists($model, 'getCodeField') ? $model->getCodeField() : 'code';

            if (empty($model->{$field})) {
                $model->{$field} = $model->generateUniqueCode();
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

        $count = static::withoutGlobalScopes()
            ->where('team_id', $this->team_id)
            ->count();

        $sequence = $count + 1;

        return "{$prefix}-{$date}-".str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Override in your model to use a different field name (default: 'code').
     */
    protected function getCodeField(): string
    {
        return 'code';
    }

    /**
     * Get the code prefix for the model.
     */
    abstract protected function getCodePrefix(): string;
}
