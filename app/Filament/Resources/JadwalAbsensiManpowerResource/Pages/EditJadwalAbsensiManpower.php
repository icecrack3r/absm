<?php

namespace App\Filament\Resources\JadwalAbsensiManpowerResource\Pages;

use App\Filament\Resources\JadwalAbsensiManpowerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalAbsensiManpower extends EditRecord
{
    protected static string $resource = JadwalAbsensiManpowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
