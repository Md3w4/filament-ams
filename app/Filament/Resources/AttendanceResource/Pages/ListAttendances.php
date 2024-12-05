<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Attendance')
                ->url(fn(): string => route('attendance.index')),
                // ->openUrlInNewTab(),
            // Actions\Action::make('checkIn')
            //     ->label('Absen Masuk')
            //     ->action(function(){
            //         return redirect()->route('filament.pages.check-in');
            //     }),
            // Actions\Action::make('checkOut')
            //     ->label('Absen Pulang')
            //     ->action(fn() => $this->record->checkOut()),
        ];
    }
}
