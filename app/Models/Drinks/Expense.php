<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Drinks\TransactionStatus;
use App\Models\User;
use Database\Factories\Drinks\ExpenseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'expense_type_id', 'amount', 'document_date', 'code',
    'label', 'observation', 'status', 'validated_at', 'validated_by', 'created_by',
])]
class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use \App\Concerns\LogsActivity, BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_expenses';

    protected function getCodePrefix(): string
    {
        return 'CHG';
    }

    /** @return BelongsTo<ExpenseType, $this> */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
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

    /** @param Builder<Expense> $q */
    public function scopeValidated(Builder $q): Builder
    {
        return $q->where('status', TransactionStatus::Validated->value);
    }

    /** @param Builder<Expense> $q */
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
            'amount' => 'decimal:2',
        ];
    }
}
