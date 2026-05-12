<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Drinks\TransactionStatus;
use App\Models\User;
use Database\Factories\Drinks\InventoryFactory;
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
class Inventory extends Model
{
    /** @use HasFactory<InventoryFactory> */
    use BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_inventories';

    protected function getCodePrefix(): string
    {
        return 'INV';
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

    /** @return HasMany<InventoryLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(InventoryLine::class);
    }

    /** @param Builder<Inventory> $q */
    public function scopeValidated(Builder $q): Builder
    {
        return $q->where('status', TransactionStatus::Validated->value);
    }

    /** @param Builder<Inventory> $q */
    public function scopeBetween(Builder $q, string $from, string $to): Builder
    {
        return $q->whereBetween('document_date', [$from, $to]);
    }

    public function isDraft(): bool
    {
        return $this->status === TransactionStatus::Draft;
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
