<?php

namespace App\Filament\Resources\JadwalAbsensiManpowerResource\Pages;

use App\Filament\Resources\JadwalAbsensiManpowerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalAbsensiManpowers extends ListRecords
{
    protected static string $resource = JadwalAbsensiManpowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
