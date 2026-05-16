<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Concerns\LogsActivity;
use Database\Factories\Drinks\SupplierFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'code', 'name', 'address', 'phone', 'contact', 'is_active'])]
class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use BelongsToTeam, HasCodeGeneration, HasFactory, LogsActivity;

    protected $table = 'drinks_suppliers';

    protected function getCodePrefix(): string
    {
        return 'FOU';
    }

    /** @return HasMany<Procurement, $this> */
    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
