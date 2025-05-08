<?php

namespace Database\Factories;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'classroom_id'  => Classroom::factory(), // atau override di seeder
            'title'         => $this->faker->sentence(4),
            'description'   => $this->faker->paragraph(2),
            'image'         => null,
            'published_at'  => now(),
            'visible_until' => now()->addDays(rand(2, 10)),
        ];
    }
}
