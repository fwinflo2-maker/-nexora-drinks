<?php

namespace App\Models\Drinks;

use Database\Factories\Drinks\LossLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['loss_id', 'article_id', 'quantity', 'cost_price'])]
class LossLine extends Model
{
    /** @use HasFactory<LossLineFactory> */
    use HasFactory;

    protected $table = 'drinks_loss_lines';

    /** @return BelongsTo<Loss, $this> */
    public function loss(): BelongsTo
    {
        return $this->belongsTo(Loss::class);
    }

    /** @return BelongsTo<Article, $this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
