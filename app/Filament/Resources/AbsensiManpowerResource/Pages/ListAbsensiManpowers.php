<?php

namespace App\Filament\Resources\AbsensiManpowerResource\Pages;

use App\Filament\Resources\AbsensiManpowerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsensiManpowers extends ListRecords
{
    protected static string $resource = AbsensiManpowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
