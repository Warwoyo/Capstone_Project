<?php

namespace Database\Factories;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(), // atau langsung classroom_id manual kalau dipakai di seeder
            'title'        => 'Tema ' . $this->faker->word(),
        ];
    }
}
