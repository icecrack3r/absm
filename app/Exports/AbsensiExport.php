<?php

namespace App\Exports;

use App\Models\AbsensiManpower;
use Maatwebsite\Excel\Concerns\FromCollection;

class AbsensiExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AbsensiManpower::all();
    }
}
