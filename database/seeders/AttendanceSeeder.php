<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $office = Office::first(); // Ambil kantor pertama
        $latitude = $office->latitude;
        $longitude = $office->longitude;
        $radius = $office->radius;

        for ($i = 1; $i <= 10; $i++) { // Buat 10 attendance
            // Generate random latitude and longitude within radius
            $latitude_in = $latitude + rand(-$radius, $radius) / 100000;
            $longitude_in = $longitude + rand(-$radius, $radius) / 100000;
            $latitude_out = $latitude + rand(-$radius, $radius) / 100000;
            $longitude_out = $longitude + rand(-$radius, $radius) / 100000;

            Attendance::factory()->create([
                'user_id' => $i, // Pastikan user ini ada
                'schedule_id' => $i, // Pastikan schedule ini ada
                'time_in' => '08:30:00',
                'latitude_in' => $latitude_in,
                'longitude_in' => $longitude_in,
                'status_in' => 'on_time',
                'time_out' => '17:05:00',
                'latitude_out' => $latitude_out,
                'longitude_out' => $longitude_out,
                'status_out' => 'overtime',
                'is_automated_checkout' => false,
            ]);
        }
    }
}
