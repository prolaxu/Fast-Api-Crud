<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserModelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => $this->faker->name,
            'email'    => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password,

        ];
    }
}
