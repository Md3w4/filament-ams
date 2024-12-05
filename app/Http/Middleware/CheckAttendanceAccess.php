<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAttendanceAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !auth()->user()->canAccessAttendance()) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}