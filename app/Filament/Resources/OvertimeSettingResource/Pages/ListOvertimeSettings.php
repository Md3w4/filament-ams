<?php

namespace App\Filament\Resources\OvertimeSettingResource\Pages;

use App\Filament\Resources\OvertimeSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOvertimeSettings extends ListRecords
{
    protected static string $resource = OvertimeSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
