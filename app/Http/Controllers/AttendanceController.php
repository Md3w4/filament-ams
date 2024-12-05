<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $schedule = Schedule::where('user_id', $user->id)
            // ->whereDate('date', today())
            ->first();

        if (!$schedule) {
            return response()->json(['message' => 'Tidak ada jadwal hari ini'], 400);
        }

        $lastAttendance = Attendance::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->whereDate('time_in', today())
            ->first();

        $overtimeRequest = OvertimeRequest::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', today())
            ->where('status', \App\Models\OvertimeRequestStatus::APPROVED)
            ->first();
            // ->get();

        return view('attendance.index', compact('user', 'schedule', 'lastAttendance', 'overtimeRequest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $schedule = Schedule::with(['shift', 'office'])->findOrFail($request->schedule_id);
        $now = Carbon::now();

        // Validate location
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $schedule->office->latitude,
            $schedule->office->longitude
        );

        if ($distance > $schedule->office->radius) {
            return response()->json(['message' => 'Anda berada di luar radius kantor.'], 400);
        }

        $lastAttendance = Attendance::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->whereDate('time_in', today())
            ->first();

        try {
            if (!$lastAttendance) {
                // Check-in process
                $status = $now->gt($schedule->shift->start_time->addMinutes($schedule->shift->late_threshold))
                    ? \App\Models\AttendanceStatus::LATE
                    : \App\Models\AttendanceStatus::ON_TIME;

                Attendance::create([
                    'user_id' => $user->id,
                    'schedule_id' => $schedule->id,
                    'time_in' => $now,
                    'latitude_in' => $request->latitude,
                    'longitude_in' => $request->longitude,
                    'status_in' => $status
                ]);

                return response()->json(['message' => 'Presensi masuk berhasil.'], 200);
            }

            // Check-out process
            if ($lastAttendance->time_out !== null) {
                return response()->json(['message' => 'Anda sudah melakukan presensi pulang hari ini.'], 400);
            }

            // Get active overtime request if exists
            $overtimeRequest = OvertimeRequest::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->whereDate('date', today())
                ->where('status', \App\Models\OvertimeRequestStatus::APPROVED)
                ->first();

            $status = $this->determineCheckoutStatus($now, $schedule, $overtimeRequest);

            // If overtime, validate against estimated end time
            if ($overtimeRequest && $status === \App\Models\AttendanceStatus::OVERTIME) {
                $now = min($now, $overtimeRequest->estimated_end_time);
            }

            $lastAttendance->update([
                'time_out' => $now,
                'latitude_out' => $request->latitude,
                'longitude_out' => $request->longitude,
                'status_out' => $status,
                'overtime_request_id' => $overtimeRequest?->id
            ]);

            return response()->json(['message' => 'Presensi pulang berhasil.'], 200);
        } catch (\Exception $e) {
            \Log::error('Attendance Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan presensi.'], 500);
        }
    }

    private function determineCheckoutStatus(Carbon $now, Schedule $schedule, ?OvertimeRequest $overtimeRequest): \App\Models\AttendanceStatus
    {
        $shiftEndTime = $schedule->shift->end_time;

        // If before shift end - early leave
        if ($now->lt($shiftEndTime)) {
            return \App\Models\AttendanceStatus::EARLY_LEAVE;
        }

        // If has approved overtime request and within estimated time
        if ($overtimeRequest && $now->gt($shiftEndTime)) {
            return \App\Models\AttendanceStatus::OVERTIME;
        }

        // Regular on-time checkout
        return \App\Models\AttendanceStatus::ON_TIME;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
