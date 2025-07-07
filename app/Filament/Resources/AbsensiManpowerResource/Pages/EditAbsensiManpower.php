<?php

namespace App\Filament\Resources\AbsensiManpowerResource\Pages;

use App\Filament\Resources\AbsensiManpowerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiManpower extends EditRecord
{
    protected static string $resource = AbsensiManpowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
