<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'overtime_request_id',
        'time_in',
        'latitude_in',
        'longitude_in',
        'status_in',
        'time_out',
        'latitude_out',
        'longitude_out',
        'status_out',
        'is_automated_checkout'
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'status_in' => AttendanceStatus::class,
        'status_out' => AttendanceStatus::class,
        'is_automated_checkout' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function overtimeRequest()
    {
        return $this->belongsTo(OvertimeRequest::class);
    }
}

enum AttendanceStatus: string
{
    case ON_TIME = 'on_time';
    case LATE = 'late';
    case EARLY_LEAVE = 'early_leave';
    case OVERTIME = 'overtime';
    case SYSTEM_GENERATED = 'system_generated';
}
