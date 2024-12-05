<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeSettingResource\Pages;
use App\Filament\Resources\OvertimeSettingResource\RelationManagers;
use App\Models\OvertimeSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OvertimeSettingResource extends Resource
{
    protected static ?string $model = OvertimeSetting::class;

    protected static ?string $navigationGroup = 'Overtime';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('office_id')
                    ->relationship('office', 'name')
                    ->required(),
                Forms\Components\TextInput::make('minimum_overtime_minutes')
                    ->required()
                    ->numeric()
                    ->default(30),
                Forms\Components\TextInput::make('maximum_overtime_hours')
                    ->required()
                    ->numeric()
                    ->default(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('office.name')
                    // ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_overtime_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maximum_overtime_hours')
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
            'index' => Pages\ListOvertimeSettings::route('/'),
            'create' => Pages\CreateOvertimeSetting::route('/create'),
            'edit' => Pages\EditOvertimeSetting::route('/{record}/edit'),
        ];
    }
}
