<?php

namespace App\Models\Drinks;

use Database\Factories\Drinks\ProcurementArticleLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['procurement_id', 'article_id', 'quantity_received', 'unit_price', 'amount'])]
class ProcurementArticleLine extends Model
{
    /** @use HasFactory<ProcurementArticleLineFactory> */
    use HasFactory;

    protected $table = 'drinks_procurement_article_lines';

    /** @return BelongsTo<Procurement, $this> */
    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    /** @return BelongsTo<Article, $this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
