<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Category;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->unique()->randomElement([
                'Bières', 'Sodas', 'Eaux minérales', 'Jus de fruits',
                'Boissons énergétiques', 'Vins', 'Spiritueux', 'Malts',
            ]).' '.fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
