<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('calculatePayroll')
                ->label('Hitung Payroll Bulanan')
                ->form([
                    DatePicker::make('month')
                        ->format('Y-m')
                        ->label('Periode Payroll')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $month = Carbon::parse($data['month'])->format('Y-m');
                    $users = User::all();

                    foreach ($users as $user) {
                        $attendances = Attendance::where('user_id', $user->id)
                            ->whereYear('date', Carbon::parse($month)->year)
                            ->whereMonth('date', Carbon::parse($month)->month)
                            ->get();

                        $daysWorked = $attendances->count();
                        $lateDays = $attendances->where('status_in', 'late')->count();
                        $overtimeHours = $attendances->where('status_out', 'overtime')
                            ->sum('time_out');
                        $absentDays = $attendances->where('status_in', 'absent')->count();

                        // Perhitungan tunjangan
                        $allowanceMeal = $daysWorked * 25000;
                        $allowanceTransport = ($daysWorked - $lateDays) * 25000;
                        $allowanceOvertime = $overtimeHours * 20000;

                        // Perhitungan potongan
                        $deductions = $absentDays * ($user->salary / 22 + 25000 + 25000);

                        // Perhitungan gaji bersih
                        $netSalary = $user->salary + $allowanceMeal +
                            $allowanceTransport + $allowanceOvertime - $deductions;

                        // Simpan atau update data payroll
                        Payroll::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'month' => $month,
                            ],
                            [
                                'basic_salary' => $user->salary,
                                'allowance_meal' => $allowanceMeal,
                                'allowance_transport' => $allowanceTransport,
                                'allowance_overtime' => $allowanceOvertime,
                                'deductions' => $deductions,
                                'net_salary' => $netSalary,
                            ]
                        );
                    }

                    Notification::make()
                        ->success()
                        ->title('Payroll berhasil dihitung')
                        ->send();
                })
        ];
    }
}
