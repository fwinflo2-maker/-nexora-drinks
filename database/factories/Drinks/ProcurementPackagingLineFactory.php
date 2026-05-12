<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Packaging;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\ProcurementPackagingLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcurementPackagingLine>
 */
class ProcurementPackagingLineFactory extends Factory
{
    protected $model = ProcurementPackagingLine::class;

    public function definition(): array
    {
        return [
            'procurement_id' => Procurement::factory(),
            'packaging_id' => Packaging::factory(),
            'quantity' => fake()->numberBetween(1, 200),
        ];
    }
}
