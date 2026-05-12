<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryItem>
 */
class DeliveryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderedQty = $this->faker->numberBetween(1, 50);

        return [
            'delivery_id' => Delivery::factory(),
            'product_id' => Product::factory(),
            'ordered_qty' => $orderedQty,
            'delivered_qty' => $orderedQty,
            'returned_qty' => 0,
            'reason_partial' => null,
        ];
    }
}
