<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Penggajian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Nama Karyawan'),
                DatePicker::make('month')
                    ->format('Y-m')
                    ->required()
                    ->label('Periode'),
                TextInput::make('basic_salary')
                    ->numeric()
                    ->required()
                    ->label('Gaji Pokok'),
                TextInput::make('allowance_meal')
                    ->numeric()
                    ->required()
                    ->label('Tunjangan Makan'),
                TextInput::make('allowance_transport')
                    ->numeric()
                    ->required()
                    ->label('Tunjangan Transport'),
                TextInput::make('allowance_overtime')
                    ->numeric()
                    ->default(0)
                    ->label('Tunjangan Lembur'),
                TextInput::make('deductions')
                    ->numeric()
                    ->default(0)
                    ->label('Potongan'),
                TextInput::make('net_salary')
                    ->numeric()
                    ->required()
                    ->label('Gaji Bersih'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Karyawan'),
                Tables\Columns\TextColumn::make('month')
                    ->date('F Y')
                    ->sortable()
                    ->label('Periode'),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->money('IDR')
                    ->sortable()
                    ->label('Gaji Pokok'),
                Tables\Columns\TextColumn::make('allowance_meal')
                    ->money('IDR')
                    ->label('Tunjangan Makan'),
                Tables\Columns\TextColumn::make('allowance_transport')
                    ->money('IDR')
                    ->label('Tunjangan Transport'),
                Tables\Columns\TextColumn::make('allowance_overtime')
                    ->money('IDR')
                    ->label('Tunjangan Lembur'),
                Tables\Columns\TextColumn::make('deductions')
                    ->money('IDR')
                    ->label('Potongan'),
                Tables\Columns\TextColumn::make('net_salary')
                    ->money('IDR')
                    ->sortable()
                    ->label('Gaji Bersih'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Karyawan'),
                Tables\Filters\Filter::make('month')
                    ->form([
                        DatePicker::make('month')
                            ->format('Y-m')
                            ->label('Periode'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['month'],
                                fn(Builder $query, $date): Builder => $query->whereMonth('month', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
