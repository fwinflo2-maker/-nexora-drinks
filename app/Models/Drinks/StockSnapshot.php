<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use Database\Factories\Drinks\StockSnapshotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'snapshot_date', 'article_id', 'cost_price', 'stock_qty'])]
class StockSnapshot extends Model
{
    /** @use HasFactory<StockSnapshotFactory> */
    use BelongsToTeam, HasFactory;

    protected $table = 'drinks_stock_snapshots';

    /** @return BelongsTo<Article, $this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date:Y-m-d',
            'stock_qty' => 'integer',
            'cost_price' => 'float',
        ];
    }
}
