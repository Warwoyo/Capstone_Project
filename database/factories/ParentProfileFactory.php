<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParentProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'  => User::factory(['role'=>'parent']),
            'relation' => $this->faker->randomElement(['father','mother']),
            'nik'      => $this->faker->numerify('##########'),
            'occupation'=> $this->faker->jobTitle(),
            'address'  => $this->faker->address(),
        ];
    }
}
