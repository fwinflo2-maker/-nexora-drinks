<?php

namespace App\Models\Drinks;

use Database\Factories\Drinks\ProcurementPackagingLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['procurement_id', 'packaging_id', 'quantity'])]
class ProcurementPackagingLine extends Model
{
    /** @use HasFactory<ProcurementPackagingLineFactory> */
    use HasFactory;

    protected $table = 'drinks_procurement_packaging_lines';

    /** @return BelongsTo<Procurement, $this> */
    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    /** @return BelongsTo<Packaging, $this> */
    public function packaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }
}
