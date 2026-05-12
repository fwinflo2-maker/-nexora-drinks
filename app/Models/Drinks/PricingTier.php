<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use Database\Factories\Drinks\PricingTierFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'article_id', 'label', 'unit_price'])]
class PricingTier extends Model
{
    /** @use HasFactory<PricingTierFactory> */
    use BelongsToTeam, HasFactory;

    protected $table = 'drinks_pricing_tiers';

    /** @return BelongsTo<Article, $this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
