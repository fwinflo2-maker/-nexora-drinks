<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Models\Drinks\Article;
use App\Models\Drinks\StockMovement;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'article_id' => Article::factory(),
            'kind' => fake()->randomElement(StockMovementKind::cases()),
            'quantity' => fake()->numberBetween(-100, 100),
            'source_type' => null,
            'source_id' => null,
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'created_by' => User::factory(),
        ];
    }
}
