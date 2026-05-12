<?php

namespace Database\Factories;

use App\Models\PackagingType;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackagingType>
 */
class PackagingTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->randomElement([
                'Casier 75cl', 'Casier 65cl', 'Casier 33cl',
                'Casier 25cl', 'Bouteille verre 1L', 'Caisse plastique',
            ]),
            'description' => fake()->optional(0.5)->sentence(),
            'unit_value_xaf' => fake()->randomElement([500, 1000, 1500, 2000, 3000, 5000]),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the packaging type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
