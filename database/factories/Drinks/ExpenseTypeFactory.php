<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\ExpenseType;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseType>
 */
class ExpenseTypeFactory extends Factory
{
    protected $model = ExpenseType::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->word(),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
