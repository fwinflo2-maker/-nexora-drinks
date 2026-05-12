<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use Database\Factories\Drinks\PackagingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'code', 'name', 'deposit_price', 'stock_qty',
    'packs_per_unit', 'is_active',
])]
class Packaging extends Model
{
    /** @use HasFactory<PackagingFactory> */
    use BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_packagings';

    protected function getCodePrefix(): string
    {
        return 'EMB';
    }

    /**
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'packaging_id');
    }

    /**
     * @return HasMany<PackagingMovement, $this>
     */
    public function movements(): HasMany
    {
        return $this->hasMany(PackagingMovement::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
