<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Enums\Drinks\StockMovementKind;
use Database\Factories\Drinks\StockMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'team_id', 'article_id', 'kind', 'quantity',
    'source_type', 'source_id', 'document_date', 'created_by',
])]
class StockMovement extends Model
{
    /** @use HasFactory<StockMovementFactory> */
    use BelongsToTeam, HasFactory;

    protected $table = 'drinks_stock_movements';

    /** @return BelongsTo<Article, $this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
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
