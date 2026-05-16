<?php

declare(strict_types=1);

namespace App\Models\Hotel;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'name', 'description', 'base_price', 'capacity', 'amenities', 'is_active'])]
class RoomType extends Model
{
    use BelongsToTeam;

    protected $table = 'hotel_room_types';

    /** @return HasMany<Room, $this> */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'amenities' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
