<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Family;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Family>
 */
class FamilyFactory extends Factory
{
    protected $model = Family::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->unique()->randomElement([
                'SABC', 'Guinness', 'Castel', 'Heineken',
                'Coca-Cola', 'Pepsi', 'Source Tangui', 'Awa',
            ]).' '.fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
