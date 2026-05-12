<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use Database\Factories\Drinks\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['team_id', 'key', 'value'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use BelongsToTeam, HasFactory;

    protected $table = 'drinks_settings';

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }
}
