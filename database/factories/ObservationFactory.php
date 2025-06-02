<?php

namespace Database\Factories;

use App\Models\ScheduleDetail;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'schedule_detail_id' => ScheduleDetail::factory(),
            'student_id'         => Student::factory(),
            'description'        => $this->faker->paragraph(),
        ];
    }
}
