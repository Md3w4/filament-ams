<?php

namespace App\Filament\Resources\OvertimeSettingResource\Pages;

use App\Filament\Resources\OvertimeSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOvertimeSetting extends EditRecord
{
    protected static string $resource = OvertimeSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
