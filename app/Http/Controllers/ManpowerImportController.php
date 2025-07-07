<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ManpowerImport;
use Symfony\Component\HttpFoundation\Response;

class ManpowerImportController extends Controller
{
    public function downloadTemplate()
    {
        $path = storage_path('app/templates/template_manpower.xlsx');
        return response()->download($path, 'template_manpower.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        Excel::import(new ManpowerImport, $request->file('file'));
        return back()->with('success', 'Data berhasil diimport!');
    }
}
