<?php

namespace Database\Factories;

use App\Models\Buyer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Buyer>
 */
class BuyerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Buyer::class;

    public function definition(): array
    {
        return [
            'business_name' => $this->faker->company(),
            'full_name' => $this->faker->company(),
            'credit_limit' => $this->faker->numberBetween(1000, 50000), // Random credit limit between 1000 and 50000
            'service_out' => $this->faker->boolean(), // True/False for service_out
            'address_1' => $this->faker->streetAddress(), // Random street address
            'phone_number' => '0' . $this->faker->numerify('#########'),
            'secondary_phone_number' => '0' . $this->faker->numerify('#########'),
            'whatsapp_number' => '0' . $this->faker->numerify('#########'),
        ];
    }
}
