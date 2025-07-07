<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjekExport;
use App\Exports\ManpowerExport;
use App\Exports\JadwalExport;
use App\Exports\AbsensiExport;

class ExportController extends Controller
{
    public function exportProjek()
    {
        return Excel::download(new ProjekExport, 'data-projek.xlsx');
    }

    public function exportManpower()
    {
        return Excel::download(new ManpowerExport, 'data-manpower.xlsx');
    }

    public function exportJadwal()
    {
        return Excel::download(new JadwalExport, 'data-jadwal-absensi.xlsx');
    }

    public function exportAbsensi()
    {
        return Excel::download(new AbsensiExport, 'data-absensi.xlsx');
    }
}