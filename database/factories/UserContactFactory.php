<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'phone_number'=> $this->faker->unique()->numerify('08##########'),
            'is_primary'  => true,
            'verified_at' => now(),
        ];
    }
}
