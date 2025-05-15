<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_number'=> $this->faker->unique()->numerify('SISWA-####'),
            'name'          => $this->faker->name(),
            'birth_date'    => $this->faker->date(),
            'gender'        => $this->faker->randomElement(['male','female']),
            'photo'         => null,
        ];
    }
}
