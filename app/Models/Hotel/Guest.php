<?php

declare(strict_types=1);

namespace App\Models\Hotel;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'name', 'email', 'phone', 'id_type', 'id_number'])]
class Guest extends Model
{
    use BelongsToTeam;

    protected $table = 'hotel_guests';

    /** @return HasMany<Reservation, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
