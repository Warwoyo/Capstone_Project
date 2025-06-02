<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $role = $this->faker->randomElement(['admin','teacher','parent']);

        return [
            'name'  => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'role'  => $role,
            'password' => bcrypt('password'),      // default pass
            'remember_token' => Str::random(10),
        ];
    }
}
