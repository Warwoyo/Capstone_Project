<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(['role'=>'teacher']),
            'nik'     => $this->faker->numerify('##########'),
            'address' => $this->faker->address(),
        ];
    }
}
