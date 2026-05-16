<?php

declare(strict_types=1);

namespace App\Models\Hotel;

use App\Concerns\BelongsToTeam;
use App\Enums\Hotel\RoomStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'room_type_id', 'number', 'floor', 'status', 'notes'])]
class Room extends Model
{
    use BelongsToTeam;

    protected $table = 'hotel_rooms';

    /** @return BelongsTo<RoomType, $this> */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /** @return HasMany<Reservation, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @param Builder<Room> $q */
    public function scopeAvailable(Builder $q): Builder
    {
        return $q->where('status', RoomStatus::Available->value);
    }

    public function isAvailable(): bool
    {
        return $this->status === RoomStatus::Available;
    }

    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
        ];
    }
}
