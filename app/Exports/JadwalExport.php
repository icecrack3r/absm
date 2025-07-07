<?php

namespace App\Exports;

use App\Models\JadwalAbsensiManpower;
use Maatwebsite\Excel\Concerns\FromCollection;

class JadwalExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return JadwalAbsensiManpower::all();
    }
}
