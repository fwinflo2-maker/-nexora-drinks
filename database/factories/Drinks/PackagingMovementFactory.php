<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\PackagingMovement;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackagingMovement>
 */
class PackagingMovementFactory extends Factory
{
    protected $model = PackagingMovement::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'packaging_id' => Packaging::factory(),
            'kind' => fake()->randomElement(StockMovementKind::cases()),
            'quantity' => fake()->numberBetween(-50, 50),
            'source_type' => null,
            'source_id' => null,
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'created_by' => User::factory(),
        ];
    }
}
