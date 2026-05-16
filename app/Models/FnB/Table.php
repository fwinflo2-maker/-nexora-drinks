<?php

declare(strict_types=1);

namespace App\Models\FnB;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'name', 'capacity', 'location', 'status'])]
class Table extends Model
{
    use BelongsToTeam;

    protected $table = 'fnb_tables';

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @param Builder<Table> $q */
    public function scopeFree(Builder $q): Builder
    {
        return $q->where('status', 'free');
    }

    public function isFree(): bool
    {
        return $this->status === 'free';
    }
}
