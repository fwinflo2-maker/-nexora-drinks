<?php

namespace Database\Factories;

use App\Enums\MovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Team;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
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
            'movement_type' => fake()->randomElement(MovementType::cases()),
            'quantity' => fake()->numberBetween(1, 100),
            'unit_cost' => fake()->optional()->numberBetween(200, 5000),
            'reference_type' => null,
            'reference_id' => null,
            'notes' => fake()->optional()->sentence(),
            'created_by' => null,
        ];
    }
}
