<?php

namespace App\Filament\Resources\ConfigurationResource\Pages;

use App\Filament\Resources\ConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConfigurations extends ManageRecords
{
    protected static string $resource = ConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
