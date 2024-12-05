<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\AttendanceResource\Pages;
use App\Filament\User\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('office_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('time_in')
                    ->required(),
                Forms\Components\TextInput::make('latitude_in')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('longitude_in')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('time_out'),
                Forms\Components\TextInput::make('latitude_out')
                    ->numeric(),
                Forms\Components\TextInput::make('longitude_out')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('latitude_in')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude_in')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('latitude_out')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude_out')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
