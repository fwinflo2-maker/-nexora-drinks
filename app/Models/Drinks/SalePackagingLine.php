<?php

namespace App\Models\Drinks;

use Database\Factories\Drinks\SalePackagingLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sale_id', 'packaging_id', 'quantity_out', 'quantity_returned', 'status'])]
class SalePackagingLine extends Model
{
    /** @use HasFactory<SalePackagingLineFactory> */
    use HasFactory;

    protected $table = 'drinks_sale_packaging_lines';

    /** @return BelongsTo<Sale, $this> */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /** @return BelongsTo<Packaging, $this> */
    public function packaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }
}
