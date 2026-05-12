<?php

namespace Database\Factories;

use App\Enums\ClientType;
use App\Models\Client;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->randomElement([
                'Bar Le Palmier', 'Resto Chez Mama', 'Boutique Ndongo',
                'Super Marché Central', 'Buvette du Coin', 'Alimentation Générale Fotso',
                'Bar Dancing Le Soleil', 'Depot Boissons Kamga', 'Restaurant Le Maquis',
                'Grossiste Tchatchoua', 'Bar Le Refuge', 'Boutique Express',
            ]),
            'phone' => fake()->numerify('+237 6## ### ###'),
            'phone2' => fake()->optional(0.3)->numerify('+237 6## ### ###'),
            'email' => fake()->optional(0.4)->safeEmail(),
            'address' => fake()->address(),
            'gps_lat' => fake()->latitude(3.5, 6.5),
            'gps_lng' => fake()->longitude(9.0, 14.0),
            'zone' => fake()->randomElement([
                'Zone Nord', 'Zone Sud', 'Centre Ville', 'Périphérie',
                'Zone Industrielle', 'Marché Central', 'Quartier Résidentiel',
            ]),
            'credit_limit' => fake()->randomElement([0, 50000, 100000, 250000, 500000]),
            'payment_terms_days' => fake()->randomElement([0, 7, 15, 30]),
            'commercial_id' => null,
            'client_type' => fake()->randomElement(ClientType::cases()),
            'is_active' => true,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Indicate that the client is a grossiste.
     */
    public function grossiste(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_type' => ClientType::Grossiste,
            'credit_limit' => fake()->randomElement([500000, 1000000, 2000000]),
            'payment_terms_days' => 30,
        ]);
    }

    /**
     * Indicate that the client is a bar.
     */
    public function bar(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_type' => ClientType::Bar,
        ]);
    }
}
