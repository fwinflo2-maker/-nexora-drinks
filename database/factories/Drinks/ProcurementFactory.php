<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\ProcurementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\Supplier;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Procurement>
 */
class ProcurementFactory extends Factory
{
    protected $model = Procurement::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'kind' => fake()->randomElement(ProcurementKind::cases()),
            'code' => fake()->unique()->bothify('APP-#####'),
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'supplier_id' => null,
            'observation' => fake()->optional()->sentence(),
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
            'total_ht' => fake()->numberBetween(5000, 500000),
            'packs_count' => fake()->numberBetween(1, 100),
            'created_by' => User::factory(),
        ];
    }

    public function validated(): static
    {
        return $this->state(fn () => [
            'status' => TransactionStatus::Validated,
            'validated_at' => now(),
        ]);
    }

    public function withSupplier(): static
    {
        return $this->state(fn () => [
            'supplier_id' => Supplier::factory(),
        ]);
    }
}
