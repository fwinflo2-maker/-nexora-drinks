<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Drinks\SaleKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\User;
use Database\Factories\Drinks\SaleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'kind', 'code', 'document_date', 'client_id',
    'observation', 'status', 'validated_at', 'validated_by',
    'created_by', 'discount_total', 'rebate_credit',
    'total_ht', 'total_ttc',
])]
class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use \App\Concerns\LogsActivity, BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_sales';

    protected function getCodePrefix(): string
    {
        return 'VTE';
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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

    /** @return HasMany<SaleArticleLine, $this> */
    public function articleLines(): HasMany
    {
        return $this->hasMany(SaleArticleLine::class);
    }

    /** @return HasMany<SalePackagingLine, $this> */
    public function packagingLines(): HasMany
    {
        return $this->hasMany(SalePackagingLine::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** @return HasMany<PaymentAdjustment, $this> */
    public function adjustments(): HasMany
    {
        return $this->hasMany(PaymentAdjustment::class);
    }

    /** @param Builder<Sale> $q */
    public function scopeValidated(Builder $q): Builder
    {
        return $q->where('status', TransactionStatus::Validated->value);
    }

    /** @param Builder<Sale> $q */
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
            'kind' => SaleKind::class,
            'status' => TransactionStatus::class,
            'document_date' => 'date',
            'validated_at' => 'datetime',
        ];
    }
}
