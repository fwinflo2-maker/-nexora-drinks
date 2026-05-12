<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Drinks\PaymentMode;
use App\Enums\Drinks\TransactionStatus;
use App\Models\User;
use Database\Factories\Drinks\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'code', 'client_id', 'amount', 'document_date',
    'mode', 'sale_id', 'status', 'validated_at', 'validated_by', 'created_by',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use \App\Concerns\LogsActivity, BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_payments';

    protected function getCodePrefix(): string
    {
        return 'REG';
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** @return BelongsTo<Sale, $this> */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
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

    /** @param Builder<Payment> $q */
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
            'mode' => PaymentMode::class,
            'status' => TransactionStatus::class,
            'document_date' => 'date',
            'validated_at' => 'datetime',
        ];
    }
}
