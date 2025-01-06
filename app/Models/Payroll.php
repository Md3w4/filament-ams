<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'basic_salary',
        'allowance_meal',
        'allowance_transport',
        'allowance_overtime',
        'deductions',
        'net_salary',
    ];

    protected $casts = [
        'month' => 'string',
        'basic_salary' => 'decimal:2',
        'allowance_meal' => 'decimal:2',
        'allowance_transport' => 'decimal:2',
        'allowance_overtime' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
