<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\Loss;
use App\Models\Drinks\LossLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LossLine>
 */
class LossLineFactory extends Factory
{
    protected $model = LossLine::class;

    public function definition(): array
    {
        return [
            'loss_id' => Loss::factory(),
            'article_id' => Article::factory(),
            'quantity' => fake()->numberBetween(1, 50),
            'cost_price' => fake()->numberBetween(300, 5000),
        ];
    }
}
