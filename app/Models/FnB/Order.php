<?php

declare(strict_types=1);

namespace App\Models\FnB;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\FnB\OrderStatus;
use App\Enums\FnB\OrderType;
use App\Models\Hotel\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'reference', 'table_id', 'reservation_id', 'order_type', 'waiter_id',
    'status', 'total', 'validated_at', 'validated_by',
    'closed_at', 'notes',
])]
class Order extends Model
{
    use BelongsToTeam, HasCodeGeneration;

    protected $table = 'fnb_orders';

    protected function getCodePrefix(): string
    {
        return 'CMD';
    }

    protected function getCodeField(): string
    {
        return 'reference';
    }

    /** @return BelongsTo<Table, $this> */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    /** @return BelongsTo<Reservation, $this> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    /** @return BelongsTo<User, $this> */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @param Builder<Order> $q */
    public function scopeActive(Builder $q): Builder
    {
        return $q->whereIn('status', [
            OrderStatus::Open->value,
            OrderStatus::Sent->value,
            OrderStatus::Preparing->value,
            OrderStatus::Ready->value,
        ]);
    }

    public function isOpen(): bool
    {
        return $this->status === OrderStatus::Open;
    }

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'order_type' => OrderType::class,
            'total' => 'decimal:2',
            'validated_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }
}
