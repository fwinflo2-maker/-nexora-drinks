<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\CashDeposit;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashDeposit>
 */
class CashDepositFactory extends Factory
{
    protected $model = CashDeposit::class;

    public function definition(): array
    {
        $cash = fake()->numberBetween(5000, 50000);

        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('VER-#####'),
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'observation' => fake()->optional()->sentence(),
            'amount_cash' => $cash,
            'amount_cheque' => fake()->numberBetween(0, 100000),
            'amount_other' => fake()->numberBetween(0, 20000),
            'total_amount' => $cash + fake()->numberBetween(0, 100000),
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
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
}
