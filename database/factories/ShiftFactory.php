<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Pagi', 'Siang', 'Normal']),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'late_threshold' => 15,
            'early_leave_threshold' => 15,
        ];
    }
}
