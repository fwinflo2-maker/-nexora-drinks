<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use Database\Factories\Drinks\FamilyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'name', 'description', 'is_active'])]
class Family extends Model
{
    /** @use HasFactory<FamilyFactory> */
    use BelongsToTeam, HasFactory;

    protected $table = 'drinks_families';

    /**
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'family_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
