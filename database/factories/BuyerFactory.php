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
            'code' => strtoupper($this->faker->unique()->bothify('BYR###')), // Random buyer code like 'BYR123'
            'name' => $this->faker->company(), // Random company name or buyer name
            'credit_limit' => $this->faker->numberBetween(1000, 50000), // Random credit limit between 1000 and 50000
            'service_out' => $this->faker->boolean(), // True/False for service_out
            'address_1' => $this->faker->streetAddress(), // Random street address
            'address_2' => $this->faker->secondaryAddress(), // Secondary address
            'phone_no' => $this->faker->phoneNumber(), // Random phone number
        ];
    }
}
