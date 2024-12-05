<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Office;
use Livewire\Livewire;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Dotswan\MapPicker\Fields\Map;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OfficeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OfficeResource\RelationManagers;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // Mendapatkan ID dari route
        $recordId = request()->route('record');

        // Mengambil record dari model Office
        $record = $recordId ? Office::find($recordId) : null;
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextArea::make('address')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Map::make('location')
                    ->label('Location')
                    ->columnSpanFull()
                    ->defaultLocation(
                        // Cek apakah record ada untuk menentukan lokasi default
                        $record ? $record->latitude : -6.175433,
                        $record ? $record->longitude : 106.827164
                    )
                    ->liveLocation(true, false, 5000)
                    ->showMarker()
                    ->markerColor("#22c55eff")
                    ->showZoomControl()
                    ->draggable()
                    ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                    ->showMyLocationButton(true)
                    ->afterStateUpdated(function ($state, $record, Set $set): void {
                        // dd($state);
                        $latitude = $state['lat'];
                        $longitude = $state['lng'];
                        $set('latitude', $latitude);
                        $set('longitude', $longitude);
                    })
                    ->zoom(15)
                    ->detectRetina()
                    ->geoMan(true)
                    ->drawCircleMarker()
                    ->drawCircle()
                    ->setColor('#3388ff')
                    ->setFilledColor('#cad9ec'),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('radius')
                    ->numeric()
                    ->afterStateUpdated(function ($state, $record, Set $set): void {
                        $set('radius', $state);
                    })
                    ->suffix('Meter')
                    ->required(),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TimePicker::make('default_start_time')
                            ->required()
                            ->label('Default Start Time')
                            ->seconds(false),

                        Forms\Components\TimePicker::make('default_end_time')
                            ->required()
                            ->label('Default End Time')
                            ->seconds(false),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('default_late_threshold')
                            ->required()
                            ->numeric()
                            ->label('Late Threshold (minutes)')
                            ->placeholder('15')
                            ->helperText('Maximum minutes allowed for late arrival'),

                        Forms\Components\TextInput::make('default_early_leave_threshold')
                            ->required()
                            ->numeric()
                            ->label('Early Leave Threshold (minutes)')
                            ->placeholder('15')
                            ->helperText('Maximum minutes allowed for early departure'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Coordinates')
                    ->formatStateUsing(fn($record) => "{$record->latitude}, {$record->longitude}")
                    ->sortable()
                    // ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('longitude')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('radius')
                    ->numeric()
                    ->suffix(' M')
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
