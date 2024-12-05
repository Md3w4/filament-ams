<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'minimum_overtime_minutes',
        'maximum_overtime_hours'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
