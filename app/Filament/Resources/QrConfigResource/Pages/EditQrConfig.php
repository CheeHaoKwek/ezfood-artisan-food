<?php

namespace App\Filament\Resources\QrConfigResource\Pages;

use App\Filament\Resources\QrConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQrConfig extends EditRecord
{
    protected static string $resource = QrConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
