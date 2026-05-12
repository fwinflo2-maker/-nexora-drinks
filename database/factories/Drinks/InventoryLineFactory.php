<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\InventoryLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryLine>
 */
class InventoryLineFactory extends Factory
{
    protected $model = InventoryLine::class;

    public function definition(): array
    {
        return [
            'inventory_id' => Inventory::factory(),
            'article_id' => Article::factory(),
            'counted_qty' => fake()->numberBetween(0, 100),
            'cost_price' => fake()->numberBetween(300, 5000),
        ];
    }
}
