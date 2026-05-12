<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\ProcurementArticleLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcurementArticleLine>
 */
class ProcurementArticleLineFactory extends Factory
{
    protected $model = ProcurementArticleLine::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 100);
        $unitPrice = fake()->numberBetween(300, 5000);

        return [
            'procurement_id' => Procurement::factory(),
            'article_id' => Article::factory(),
            'quantity_received' => $qty,
            'unit_price' => $unitPrice,
            'amount' => $qty * $unitPrice,
        ];
    }
}
