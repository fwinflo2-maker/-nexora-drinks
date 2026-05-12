<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockLevel;
use App\Models\Team;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockLevel>
 */
class StockLevelFactory extends Factory
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
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity' => fake()->numberBetween(0, 500),
            'reserved_quantity' => 0,
            'min_threshold' => fake()->numberBetween(10, 50),
            'max_threshold' => fake()->numberBetween(400, 1000),
        ];
    }

    /**
     * Indicate that stock is critically low.
     */
    public function low(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => fake()->numberBetween(0, 5),
            'min_threshold' => 10,
        ]);
    }
}
