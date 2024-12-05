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
            'name' => 'Kantor Pusat',
            'address' => 'Jl. Raya No. 123, Jakarta',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'radius' => 100,
            'default_start_time' => '08:00:00',
            'default_end_time' => '17:00:00',
            'default_late_threshold' => '09:00:00',
            'default_early_leave_threshold' => '16:00:00',
        ]);
    }
}
