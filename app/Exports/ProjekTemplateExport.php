<?php

// File: app/Exports/ProjekTemplateExport.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjekTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'Proyek Pembangunan Jalan',
                'PRJ001',
                'Budi Santoso'
            ],
            [
                'Proyek Pembangunan Gedung',
                'PRJ002',
                'Siti Nurhaliza'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_projek',
            'kode_projek',
            'nama_lengkap_pic'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 25,
        ];
    }
}

