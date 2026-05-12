<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\Category;
use App\Models\Drinks\Packaging;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $cost = fake()->numberBetween(300, 5000);

        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('ART-#####'),
            'name' => fake()->randomElement([
                '33 Export 65cl', 'Castel Beer 65cl', 'Guinness 33cl',
                'Coca-Cola 50cl', 'Pepsi 1L', 'Tangui 1.5L',
                'Heineken 33cl', 'Mützig 65cl',
            ]),
            'brand' => fake()->optional()->company(),
            'category_id' => null,
            'packaging_id' => null,
            'sale_price' => $cost + fake()->numberBetween(100, 1000),
            'retail_price' => $cost + fake()->numberBetween(50, 500),
            'cost_price' => $cost,
            'stock_qty' => fake()->numberBetween(0, 1000),
            'frigo_stock_qty' => fake()->numberBetween(0, 200),
            'packs_per_unit' => fake()->randomElement([1, 6, 12, 24]),
            'discount_rate' => fake()->randomFloat(2, 0, 10),
            'rebate_rate' => fake()->randomFloat(2, 0, 5),
            'is_active' => true,
            'is_consignable' => fake()->boolean(70),
        ];
    }

    public function withRelations(): static
    {
        return $this->state(fn () => [
            'category_id' => Category::factory(),
            'packaging_id' => Packaging::factory(),
        ]);
    }
}
