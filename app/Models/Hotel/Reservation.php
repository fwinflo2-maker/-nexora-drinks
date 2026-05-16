<?php

declare(strict_types=1);

namespace App\Models\Hotel;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Enums\Hotel\ReservationStatus;
use App\Models\FnB\Order as FnBOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'reference', 'room_id', 'guest_id',
    'check_in', 'check_out', 'nights', 'status',
    'total_price', 'paid_amount',
    'validated_at', 'validated_by',
    'cancelled_at', 'cancelled_by', 'notes',
])]
class Reservation extends Model
{
    use BelongsToTeam, HasCodeGeneration;

    protected $table = 'hotel_reservations';

    protected function getCodePrefix(): string
    {
        return 'RES';
    }

    protected function getCodeField(): string
    {
        return 'reference';
    }

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsTo<Guest, $this> */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /** @return BelongsTo<User, $this> */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /** @return BelongsTo<User, $this> */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /** @return HasMany<Folio, $this> */
    public function folios(): HasMany
    {
        return $this->hasMany(Folio::class);
    }

    /** @return HasMany<FnBOrder, $this> */
    public function fnbOrders(): HasMany
    {
        return $this->hasMany(FnBOrder::class, 'reservation_id');
    }

    /** @param Builder<Reservation> $q */
    public function scopeArrivingToday(Builder $q): Builder
    {
        return $q->whereDate('check_in', today());
    }

    /** @param Builder<Reservation> $q */
    public function scopeDepartingToday(Builder $q): Builder
    {
        return $q->whereDate('check_out', today());
    }

    public function isCheckedIn(): bool
    {
        return $this->status === ReservationStatus::CheckedIn;
    }

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'status' => ReservationStatus::class,
            'total_price' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'validated_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
