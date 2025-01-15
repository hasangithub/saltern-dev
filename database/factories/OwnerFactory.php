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
                'nic' => $this->faker->randomElement([
                $this->faker->regexify('[0-9]{9}[VX]'), // Old format
                $this->faker->numerify('############')  // New format
                ]),
                'phone_number' => '0' . $this->faker->numerify('#########'),
                'secondary_phone_number' => '0' . $this->faker->numerify('#########'),
                'whatsapp_number' => '0' . $this->faker->numerify('#########'),
                'email' => $this->faker->unique()->safeEmail,
                'address_line_1' => $this->faker->streetAddress,
                'created_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
                'updated_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
        ];
    }
}
