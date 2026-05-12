<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Drinks\TransactionStatus;
use App\Models\User;
use Database\Factories\Drinks\CashDepositFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'code', 'document_date', 'observation',
    'amount_cash', 'amount_cheque', 'amount_other', 'total_amount',
    'status', 'validated_at', 'validated_by', 'created_by',
])]
class CashDeposit extends Model
{
    /** @use HasFactory<CashDepositFactory> */
    use BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_cash_deposits';

    protected function getCodePrefix(): string
    {
        return 'VRS';
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

    /** @param Builder<CashDeposit> $q */
    public function scopeValidated(Builder $q): Builder
    {
        return $q->where('status', TransactionStatus::Validated->value);
    }

    /** @param Builder<CashDeposit> $q */
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
