<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Schedule;
use App\Models\OvertimeRequest;
use App\Models\OvertimeSetting;
use App\Models\OvertimeRequestStatus;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OvertimeRequestResource\Pages;
use App\Filament\Resources\OvertimeRequestResource\RelationManagers;

class OvertimeRequestResource extends Resource
{
    protected static ?string $model = OvertimeRequest::class;

    protected static ?string $navigationGroup = 'Overtime';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('schedule_id')
                    ->relationship('schedule', 'id')
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                        // Ambil schedule berdasarkan ID yang dipilih
                        $schedule = Schedule::find($state);
                        if ($schedule) {
                            // Set user_id ke input hidden
                            $set('user_id', $schedule->user_id);
                        }
                    }),
                Forms\Components\Hidden::make('user_id'),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TimePicker::make('estimated_start_time')
                    ->required()
                    ->seconds(false)
                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                        $schedule = Schedule::find($get('schedule_id'));
                        if ($schedule && Carbon::parse($state)->lt($schedule->shift->end_time)) {
                            $set('estimated_start_time', null);
                            Notification::make()
                                ->title('Invalid start time')
                                ->body('Overtime must start after shift end time')
                                ->danger()
                                ->send();
                        }
                    }),
                Forms\Components\TimePicker::make('estimated_end_time')
                    ->required()
                    ->seconds(false)
                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                        $schedule = Schedule::find($get('schedule_id'));
                        if ($schedule) {
                            $overtimeSetting = OvertimeSetting::where('office_id', $schedule->office_id)->first();

                            if ($overtimeSetting) {
                                $maxOvertime = $overtimeSetting->maximum_overtime_hours;
                                $shiftEndTime = Carbon::parse($schedule->shift->end_time);
                                $maxEndTime = $shiftEndTime->copy()->addHours($maxOvertime);
                                $estimatedEndTime = Carbon::parse($state);

                                // Validasi: estimated_end_time tidak boleh kurang dari end_time shift
                                if ($estimatedEndTime->lt($shiftEndTime)) {
                                    $set('estimated_end_time', null);
                                    Notification::make()
                                        ->title('Invalid end time')
                                        ->body("Overtime end time cannot be earlier than shift end time (" . $shiftEndTime->format('H:i') . ")")
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                if ($estimatedEndTime->gt($maxEndTime)) {
                                    $set('estimated_end_time', null);
                                    Notification::make()
                                        ->title('Invalid end time')
                                        ->body("Overtime cannot end after " . $maxEndTime->format('H:i'))
                                        ->danger()
                                        ->send();
                                } else {
                                    $estimatedStartTime = Carbon::parse($get('estimated_start_time'));
                                    if ($estimatedEndTime->diffInHours($estimatedStartTime) > $maxOvertime) {
                                        $set('estimated_end_time', null);
                                        Notification::make()
                                            ->title('Invalid end time')
                                            ->body("Overtime cannot exceed {$maxOvertime} hours")
                                            ->danger()
                                            ->send();
                                    }
                                }
                            } else {
                                Notification::make()
                                    ->title('Overtime Setting Not Found')
                                    ->body('No overtime settings found for the selected office.')
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),
                // Forms\Components\TimePicker::make('estimated_end_time')
                //     ->required()
                //     ->seconds(false)
                //     ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                //         $schedule = Schedule::find($get('schedule_id'));
                //         if ($schedule) {
                //             // Ambil overtimeSetting berdasarkan office_id
                //             $overtimeSetting = \App\Models\OvertimeSetting::where('office_id', $schedule->office_id)->first();

                //             // Pastikan overtimeSetting tidak null
                //             if ($overtimeSetting) {
                //                 $maxOvertime = $overtimeSetting->maximum_overtime_hours;
                //                 $startTime = Carbon::parse($get('estimated_start_time'));
                //                 $endTime = Carbon::parse($state);

                //                 if ($endTime->diffInHours($startTime) > $maxOvertime) {
                //                     $set('estimated_end_time', null);
                //                     Notification::make()
                //                         ->title('Invalid end time')
                //                         ->body("Overtime cannot exceed {$maxOvertime} hours")
                //                         ->danger()
                //                         ->send();
                //                 }
                //             } else {
                //                 // Jika overtimeSetting null, Anda bisa memberikan notifikasi atau penanganan lain
                //                 Notification::make()
                //                     ->title('Overtime Setting Not Found')
                //                     ->body('No overtime settings found for the selected office.')
                //                     ->danger()
                //                     ->send();
                //             }
                //         }
                //     }),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('date'),
                Tables\Columns\TextColumn::make('estimated_start_time'),
                Tables\Columns\TextColumn::make('estimated_end_time'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => OvertimeRequestStatus::PENDING,
                        'success' => OvertimeRequestStatus::APPROVED,
                        'danger' => OvertimeRequestStatus::REJECTED,
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->action(fn(OvertimeRequest $record) => $record->update([
                        'status' => OvertimeRequestStatus::APPROVED,
                        'approved_by' => auth()->id()
                    ]))
                    ->requiresConfirmation()
                    ->visible(fn(OvertimeRequest $record) => $record->status === OvertimeRequestStatus::PENDING),
                Tables\Actions\Action::make('reject')
                    ->action(fn(OvertimeRequest $record) => $record->update([
                        'status' => OvertimeRequestStatus::REJECTED,
                        'approved_by' => auth()->id()
                    ]))
                    ->requiresConfirmation()
                    ->visible(fn(OvertimeRequest $record) => $record->status === OvertimeRequestStatus::PENDING),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOvertimeRequests::route('/'),
            'create' => Pages\CreateOvertimeRequest::route('/create'),
            'edit' => Pages\EditOvertimeRequest::route('/{record}/edit'),
        ];
    }
}
