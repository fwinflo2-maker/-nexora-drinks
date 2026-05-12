<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Enums\Drinks\TransactionStatus;
use Database\Factories\Drinks\PaymentAdjustmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'sale_id', 'amount', 'observation', 'status', 'created_by'])]
class PaymentAdjustment extends Model
{
    /** @use HasFactory<PaymentAdjustmentFactory> */
    use BelongsToTeam, HasFactory;

    protected $table = 'drinks_payment_adjustments';

    /** @return BelongsTo<Sale, $this> */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    protected function casts(): array
    {
        return [
            'status' => TransactionStatus::class,
        ];
    }
}
