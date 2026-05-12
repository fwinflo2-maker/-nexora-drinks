<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Packaging;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Packaging>
 */
class PackagingFactory extends Factory
{
    protected $model = Packaging::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('EMB-####'),
            'name' => fake()->randomElement([
                'Casier 12 bouteilles 65cl',
                'Casier 24 bouteilles 33cl',
                'Pack 6 PET 1.5L',
                'Bouteille consignée 1L',
            ]),
            'deposit_price' => fake()->numberBetween(500, 3000),
            'stock_qty' => fake()->numberBetween(0, 500),
            'packs_per_unit' => fake()->numberBetween(1, 24),
            'is_active' => true,
        ];
    }
}
