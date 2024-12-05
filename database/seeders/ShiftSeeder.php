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
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'late_threshold' => '09:00:00',
            'early_leave_threshold' => '16:00:00',
        ]);
    }
}
