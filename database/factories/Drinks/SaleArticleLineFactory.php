<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\Sale;
use App\Models\Drinks\SaleArticleLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleArticleLine>
 */
class SaleArticleLineFactory extends Factory
{
    protected $model = SaleArticleLine::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 50);
        $unitPrice = fake()->numberBetween(500, 10000);

        return [
            'sale_id' => Sale::factory(),
            'article_id' => Article::factory(),
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'amount_ht' => $qty * $unitPrice,
            'amount_ttc' => (int) ($qty * $unitPrice * 1.1925),
            'observation' => fake()->optional()->sentence(),
        ];
    }
}
