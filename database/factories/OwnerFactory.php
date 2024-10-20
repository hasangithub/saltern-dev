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
            'dob' => $this->faker->date,
            'nic' => $this->faker->unique()->numerify('###########'), // Assuming NIC is a numeric value
            'address' => $this->faker->address,
            'mobile_no' => $this->faker->phoneNumber,
        ];
    }
}
