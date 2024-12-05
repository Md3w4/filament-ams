<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
class OfficeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-6.1, -6.3),  // Jakarta area
            'longitude' => fake()->longitude(106.7, 106.9),  // Jakarta area
            'radius' => 100, // 100 meters
            'default_start_time' => '08:00:00',
            'default_end_time' => '17:00:00',
            'default_late_threshold' => 15, // 15 minutes
            'default_early_leave_threshold' => 15, // 15 minutes
        ];
    }
}
