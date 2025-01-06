<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::factory()->create([
            'name' => 'HIMPUH',
            'address' => 'Jl. Asem Raya No.125 Jakarta',
            'latitude' => -6.2417962236836,
            'longitude' => 106.86044499278,
            'radius' => 100,
            'default_start_time' => '09:00:00',
            'default_end_time' => '17:00:00',
            'default_late_threshold' => '09:15:00',
            'default_early_leave_threshold' => '16:45:00',
        ]);
    }
}
