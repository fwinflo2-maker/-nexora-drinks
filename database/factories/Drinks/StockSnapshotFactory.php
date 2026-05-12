<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\StockSnapshot;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockSnapshot>
 */
class StockSnapshotFactory extends Factory
{
    protected $model = StockSnapshot::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'snapshot_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'article_id' => Article::factory(),
            'cost_price' => fake()->numberBetween(300, 5000),
            'stock_qty' => fake()->numberBetween(0, 1000),
        ];
    }
}
