<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Packaging;
use App\Models\Drinks\Sale;
use App\Models\Drinks\SalePackagingLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalePackagingLine>
 */
class SalePackagingLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'packaging_id' => Packaging::factory(),
            'quantity_out' => fake()->numberBetween(1, 50),
            'quantity_returned' => fake()->numberBetween(0, 20),
            'status' => fake()->randomElement(['pending', 'returned', 'closed']),
        ];
    }
}
