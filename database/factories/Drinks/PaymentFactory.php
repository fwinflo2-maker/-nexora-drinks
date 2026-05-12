<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\PaymentMode;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Client;
use App\Models\Drinks\Payment;
use App\Models\Drinks\Sale;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('PAY-#####'),
            'client_id' => Client::factory(),
            'amount' => fake()->numberBetween(5000, 100000),
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'mode' => fake()->randomElement(PaymentMode::cases()),
            'sale_id' => fake()->optional()->boolean(0.7) ? Sale::factory() : null,
            'status' => TransactionStatus::Draft,
            'created_by' => User::factory(),
        ];
    }
}
