<?php

namespace App\Exports;

use App\Models\Manpower;
use Maatwebsite\Excel\Concerns\FromCollection;

class ManpowerExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Manpower::all();
    }
}
