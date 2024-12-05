<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Attendance;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationGroup = 'Attendance';
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?int $navigationSort = 1;

    // public $user = Auth::user();

    public static function form(Form $form): Form
    {
        // $user = Auth::user();

        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(Auth::id())
                    // ->disabled()
                    ->required(),
                // Forms\Components\Select::make('office_id')
                //     ->relationship('office', 'name')
                //     ->required(),

                // Forms\Components\DatePicker::make('date')
                //     ->required(),
                Forms\Components\TimePicker::make('time_in')
                    ->required(),
                Forms\Components\TimePicker::make('time_out'),
                Forms\Components\TextInput::make('latitude_in')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('longitude_in')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('latitude_out')
                    ->numeric(),
                Forms\Components\TextInput::make('longitude_out')
                    ->numeric(),
                // Forms\Components\Select::make('status')
                //     ->options([
                //         'on_time' => 'On Time',
                //         'late' => 'Late',
                //         'early' => 'Early',
                //     ])
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('office.name')
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('date')
                //     ->sortable(),
                Tables\Columns\TextColumn::make('time_in')
                    ->label('Jam Masuk')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_in')
                    ->label('Status Masuk')
                    // ->state(function ($record) {
                    //     return $record->time_in && $record->time_in->hour >= 10 ? 'Terlambat' : 'Tepat Waktu';
                    // })
                    ->badge()
                    // ->color(fn(string $state): string => $state === 'Terlambat' ? 'danger' : 'success'),
                    ->colors([
                        'success' => \App\Models\AttendanceStatus::ON_TIME,
                        'danger' => \App\Models\AttendanceStatus::LATE,
                        'warning' => \App\Models\AttendanceStatus::EARLY_LEAVE,
                        'primary' => \App\Models\AttendanceStatus::OVERTIME,
                        'gray' => \App\Models\AttendanceStatus::SYSTEM_GENERATED,
                    ]),
                Tables\Columns\TextColumn::make('latitude_in')
                    ->label('Coordinates In')
                    ->formatStateUsing(fn($record) => "{$record->latitude_in}, {$record->longitude_in}")
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_out')
                    ->label('Jam Pulang')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_out')
                    ->label('Status Pulang')
                    ->badge()
                    ->colors([
                        'success' => \App\Models\AttendanceStatus::ON_TIME,
                        'danger' => \App\Models\AttendanceStatus::LATE,
                        'warning' => \App\Models\AttendanceStatus::EARLY_LEAVE,
                        'info' => \App\Models\AttendanceStatus::OVERTIME,
                        'gray' => \App\Models\AttendanceStatus::SYSTEM_GENERATED,
                    ]),
                // ->state(function ($record) {
                //     if (!$record->time_out) return 'Belum Pulang';
                //     if ($record->time_out->hour >= 19) return 'Lembur';
                //     return $record->time_out->hour >= 17 ? 'Tepat Waktu' : 'Pulang Cepat';
                // })
                // ->badge()
                // ->color(fn(string $state): string => match ($state) {
                //     'Tepat Waktu' => 'success',
                //     'Pulang Cepat' => 'warning',
                //     'Belum Pulang' => 'danger',
                //     'Lembur' => 'success'
                // }),
                Tables\Columns\TextColumn::make('latitude_out')
                    ->label('Coordinates Out')
                    ->formatStateUsing(fn($record) => "{$record->latitude_out}, {$record->longitude_out}")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_automated_checkout')
                    ->label('Checkout Sistem')
                    ->boolean(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if (!$user->hasRole('super_admin')) {
                    $query->where('user_id', $user->id);
                }
            });
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
