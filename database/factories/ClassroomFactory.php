<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => 'Kelas '.$this->faker->randomElement(['A','B','C','Pelangi','Bintang']),
            'description' => $this->faker->sentence(),
            'owner_id'    => User::factory(['role'=>'teacher']),
        ];
    }
}
