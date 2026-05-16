<?php

declare(strict_types=1);

namespace App\Models\Hotel;

use App\Concerns\BelongsToTeam;
use App\Enums\Hotel\FolioType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'reservation_id', 'label', 'amount', 'type'])]
class Folio extends Model
{
    use BelongsToTeam;

    protected $table = 'hotel_folios';

    /** @return BelongsTo<Reservation, $this> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => FolioType::class,
        ];
    }
}
