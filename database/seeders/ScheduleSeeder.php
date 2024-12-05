<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) { // Buat 10 schedule
            Schedule::factory()->create([
                'user_id' => $i, // Pastikan user ini ada
                'office_id' => 1, // Pastikan office ini ada
                'shift_id' => 1, // Pastikan shift ini ada
            ]);
        }
    }
}
