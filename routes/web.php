<?php

use Illuminate\Support\Facades\Route;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AttendanceResource;
use App\Http\Controllers\AttendanceController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');

Route::middleware(['auth', 'filament.redirect'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
});

// Route::get('/admin/attendances', function () {
//     return app(ListRecords::class, ['resource' => AttendanceResource::class])
//         ->bootComponents()
//         ->render();
// })->middleware(['auth', 'filament']);
