<?php

namespace App\Exports;

use App\Models\Projek;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProjekExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Projek::all();
    }
}
