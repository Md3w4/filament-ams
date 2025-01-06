<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::factory()->create([
            'name' => 'Pagi',
            'start_time' => '9:00:00',
            'end_time' => '17:00:00',
            'default_late_threshold' => '09:15:00',
            'default_early_leave_threshold' => '16:45:00',
        ]);

        Shift::factory()->create([
            'name' => 'Lembur',
            'start_time' => '09:00:00',
            'end_time' => '21:00:00',
            'default_late_threshold' => '09:15:00',
            'default_early_leave_threshold' => '20:45:00',
        ]);
    }
}
