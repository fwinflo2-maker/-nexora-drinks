<?php

namespace Database\Factories;

use App\Enums\BaseUnit;
use App\Models\Category;
use App\Models\Product;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchasePrice = fake()->numberBetween(200, 5000);
        $salePrice = $purchasePrice * fake()->randomFloat(2, 1.15, 1.60);

        return [
            'team_id' => Team::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->randomElement([
                'Castel Beer 65cl', '33 Export 33cl', 'Beaufort Lager 65cl',
                'Coca-Cola 35cl', 'Fanta Orange 35cl', 'Sprite 35cl',
                'Tangui 1.5L', 'Supermont 0.5L', 'Source du Pays 1L',
                'Top Ananas 33cl', 'Pamplemousse 25cl', 'Malta Guinness 33cl',
                'Guinness Smooth 33cl', 'Kadji Beer 65cl', 'Isenbeck 33cl',
                'Orangina 33cl', 'Schweppes Tonic 25cl', 'Red Bull 25cl',
            ]),
            'sku' => strtoupper(fake()->unique()->bothify('NXR-###-???')),
            'barcode' => fake()->optional(0.7)->ean13(),
            'description' => fake()->optional()->sentence(),
            'image_path' => null,
            'base_unit' => BaseUnit::Bouteille,
            'units_per_pack' => fake()->randomElement([6, 12]),
            'units_per_case' => fake()->randomElement([12, 24]),
            'units_per_pallet' => fake()->randomElement([40, 48, 60]),
            'purchase_price' => round($purchasePrice, 2),
            'sale_price' => round($salePrice, 2),
            'min_sale_price' => round($purchasePrice * 1.10, 2),
            'vat_rate' => fake()->randomElement([0, 19.25]),
            'is_consignable' => fake()->boolean(70),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product is consignable.
     */
    public function consignable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_consignable' => true,
        ]);
    }
}
