<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Setting;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'key' => fake()->unique()->slug(2),
            'value' => ['enabled' => true, 'rate' => fake()->randomFloat(2, 0, 100)],
        ];
    }
}
