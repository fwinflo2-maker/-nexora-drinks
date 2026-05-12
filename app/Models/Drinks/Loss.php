<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Drinks\TransactionStatus;
use App\Models\User;
use Database\Factories\Drinks\LossFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'code', 'document_date', 'observation',
    'status', 'validated_at', 'validated_by', 'created_by',
])]
class Loss extends Model
{
    /** @use HasFactory<LossFactory> */
    use BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_losses';

    protected function getCodePrefix(): string
    {
        return 'PER';
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /** @return HasMany<LossLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(LossLine::class);
    }

    /** @param Builder<Loss> $q */
    public function scopeValidated(Builder $q): Builder
    {
        return $q->where('status', TransactionStatus::Validated->value);
    }

    /** @param Builder<Loss> $q */
    public function scopeBetween(Builder $q, string $from, string $to): Builder
    {
        return $q->whereBetween('document_date', [$from, $to]);
    }

    public function isDraft(): bool
    {
        return $this->status === TransactionStatus::Draft;
    }

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute(): float
    {
        return $this->lines->sum(fn ($l) => $l->quantity * ($l->cost_price ?? $l->article?->cost_price ?? 0));
    }

    protected function casts(): array
    {
        return [
            'status' => TransactionStatus::class,
            'document_date' => 'date',
            'validated_at' => 'datetime',
        ];
    }
}
