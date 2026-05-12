<?php

namespace App\Models\Drinks;

use Database\Factories\Drinks\SaleArticleLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sale_id', 'article_id', 'quantity', 'unit_price',
    'amount_ht', 'amount_ttc', 'observation',
])]
class SaleArticleLine extends Model
{
    /** @use HasFactory<SaleArticleLineFactory> */
    use HasFactory;

    protected $table = 'drinks_sale_article_lines';

    /** @return BelongsTo<Sale, $this> */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /** @return BelongsTo<Article, $this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
