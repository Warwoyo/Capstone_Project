<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleDetailFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');

        return [
            'schedule_id' => Schedule::factory(),
            'sub_title'   => 'Sub '.$this->faker->word(),
            'start_date'  => $start,
            'end_date'    => (clone $start)->modify('+5 days'),
            'week'        => $this->faker->numberBetween(1,4),
        ];
    }
}
