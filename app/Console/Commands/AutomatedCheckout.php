<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Console\Command;

class AutomatedCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:automated-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automated checkout for missing attendance records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        Attendance::whereDate('time_in', $today)
            ->whereNull('time_out')
            ->each(function ($attendance) {
                $attendance->update([
                    'time_out' => Carbon::now(),
                    'status_out' => \App\Models\AttendanceStatus::SYSTEM_GENERATED,
                    'is_automated_checkout' => true,
                    'latitude_out' => $attendance->latitude_in,
                    'longitude_out' => $attendance->longitude_in
                ]);
            });

        $this->info('Automated checkout completed successfully.');
    }
}
