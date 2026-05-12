<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\PricingTier;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PricingTier>
 */
class PricingTierFactory extends Factory
{
    protected $model = PricingTier::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'article_id' => Article::factory(),
            'label' => fake()->randomElement(['Détail', 'Demi-gros', 'Gros', 'Promo']),
            'unit_price' => fake()->numberBetween(500, 10000),
        ];
    }
}
