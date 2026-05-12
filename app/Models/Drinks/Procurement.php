<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Drinks\ProcurementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\User;
use Database\Factories\Drinks\ProcurementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'kind', 'code', 'document_date', 'supplier_id',
    'observation', 'status', 'validated_at', 'validated_by',
    'created_by', 'total_ht', 'packs_count',
])]
class Procurement extends Model
{
    /** @use HasFactory<ProcurementFactory> */
    use \App\Concerns\LogsActivity, BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_procurements';

    protected function getCodePrefix(): string
    {
        return 'APP';
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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

    /** @return HasMany<ProcurementArticleLine, $this> */
    public function articleLines(): HasMany
    {
        return $this->hasMany(ProcurementArticleLine::class);
    }

    /** @return HasMany<ProcurementPackagingLine, $this> */
    public function packagingLines(): HasMany
    {
        return $this->hasMany(ProcurementPackagingLine::class);
    }

    /** @param Builder<Procurement> $q */
    public function scopeValidated(Builder $q): Builder
    {
        return $q->where('status', TransactionStatus::Validated->value);
    }

    /** @param Builder<Procurement> $q */
    public function scopeBetween(Builder $q, string $from, string $to): Builder
    {
        return $q->whereBetween('document_date', [$from, $to]);
    }

    public function isValidated(): bool
    {
        return $this->status === TransactionStatus::Validated;
    }

    public function isDraft(): bool
    {
        return $this->status === TransactionStatus::Draft;
    }

    protected function casts(): array
    {
        return [
            'kind' => ProcurementKind::class,
            'status' => TransactionStatus::class,
            'document_date' => 'date',
            'validated_at' => 'datetime',
        ];
    }
}
