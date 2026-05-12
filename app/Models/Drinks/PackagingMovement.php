<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Enums\Drinks\StockMovementKind;
use Database\Factories\Drinks\PackagingMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'team_id', 'packaging_id', 'kind', 'quantity',
    'source_type', 'source_id', 'document_date', 'created_by',
])]
class PackagingMovement extends Model
{
    /** @use HasFactory<PackagingMovementFactory> */
    use BelongsToTeam, HasFactory;

    protected $table = 'drinks_packaging_movements';

    /** @return BelongsTo<Packaging, $this> */
    public function packaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    /** @return MorphTo<Model, $this> */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'kind' => StockMovementKind::class,
            'document_date' => 'date',
        ];
    }
}
