<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Shift;
use App\Models\Office;
use App\Models\Schedule;
use App\Models\OvertimeSetting;
use Illuminate\Database\Seeder;
use Database\Seeders\OfficeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call([
        //     PositionSeeder::class,
        //     OfficeSeeder::class,
        //     ShiftSeeder::class,
        //     UserSeeder::class,
        //     ScheduleSeeder::class,
        //     AttendanceSeeder::class,
        // ]);


        // Create Office
        $office = $this->call(OfficeSeeder::class);
        // $office = Office::factory()->create([
        //     'name' => 'Kantor Pusat',
        //     'latitude' => -6.2,  // Set sesuai lokasi testing
        //     'longitude' => 106.8,
        // ]);

        // Create Shift
        $shift = Shift::factory()->create([
            'name' => 'Normal',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        // Create Overtime Setting
        OvertimeSetting::factory()->create([
            'office_id' => $office->id,
            'minimum_overtime_minutes' => 30,
            'maximum_overtime_hours' => 4,
        ]);

        // Create User & Schedule
        $user = $this->call(UserSeeder::class);
        // $user = User::factory()->create([
        //     'name' => 'John Doe',
        //     'email' => 'john@example.com',
        //     'password' => bcrypt('password'),
        // ]);

        Schedule::factory()->create([
            'user_id' => $user->id,
            'office_id' => $office->id,
            'shift_id' => $shift->id,
        ]);
    }
}
