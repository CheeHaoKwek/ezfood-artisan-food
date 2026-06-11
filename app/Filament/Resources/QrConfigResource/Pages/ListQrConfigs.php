<?php

namespace App\Filament\Resources\QrConfigResource\Pages;

use App\Filament\Resources\QrConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQrConfigs extends ListRecords
{
    protected static string $resource = QrConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
