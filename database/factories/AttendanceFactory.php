<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Pastikan user ini ada
            'schedule_id' => 1, // Pastikan schedule ini ada
            'time_in' => '08:30:00',
            'latitude_in' => -6.200000,
            'longitude_in' => 106.816666,
            'status_in' => 'late',
            'time_out' => '17:05:00',
            'latitude_out' => -6.200000,
            'longitude_out' => 106.816666,
            'status_out' => 'overtime',
            'is_automated_checkout' => false,
        ];
    }
}
