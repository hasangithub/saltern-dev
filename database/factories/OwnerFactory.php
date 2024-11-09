<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

class OwnerFactory extends Factory
{
    protected $model = Owner::class;

    public function definition()
    {
        return [
            'full_name' => $this->faker->name,
                'gender' => $this->faker->randomElement(['male', 'female']),
                'civil_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
                'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                'nic' => strtoupper($this->faker->regexify('[A-Z0-9]{10}')),
                'phone_number' => $this->faker->phoneNumber,
                'secondary_phone_number' => $this->faker->optional()->phoneNumber,
                'email' => $this->faker->unique()->safeEmail,
                'address_line_1' => $this->faker->streetAddress,
                'address_line_2' => $this->faker->optional()->secondaryAddress,
        ];
    }
}
