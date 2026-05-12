<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\PaymentAdjustment;
use App\Models\Drinks\Sale;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentAdjustment>
 */
class PaymentAdjustmentFactory extends Factory
{
    protected $model = PaymentAdjustment::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'sale_id' => Sale::factory(),
            'amount' => fake()->numberBetween(1000, 50000),
            'observation' => fake()->optional()->sentence(),
            'status' => TransactionStatus::Draft,
            'created_by' => User::factory(),
        ];
    }
}
