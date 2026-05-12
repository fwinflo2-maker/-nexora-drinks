<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\SaleKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Client;
use App\Models\Drinks\Sale;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $totalHt = fake()->numberBetween(1000, 200000);

        return [
            'team_id' => Team::factory(),
            'kind' => fake()->randomElement(SaleKind::cases()),
            'code' => fake()->unique()->bothify('VTE-#####'),
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'client_id' => null,
            'observation' => fake()->optional()->sentence(),
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
            'discount_total' => fake()->numberBetween(0, 5000),
            'rebate_credit' => fake()->numberBetween(0, 2000),
            'total_ht' => $totalHt,
            'total_ttc' => (int) ($totalHt * 1.1925),
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

    public function withClient(): static
    {
        return $this->state(fn () => [
            'client_id' => Client::factory(),
        ]);
    }
}
