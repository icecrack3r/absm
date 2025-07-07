<?php

// File: app/Exports/ManpowerTemplateExport.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManpowerTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'NIP001',
                'John Doe',
                'L',
                'PRJ001',
                'Admin',
                'john@example.com',
                'password123'
            ],
            [
                'NIP002',
                'Jane Smith',
                'P',
                'PRJ002',
                'Manager',
                'jane@example.com',
                'password456'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nip',
            'nama_lengkap',
            'jenis_kelamin',
            'kode_projek',
            'nama_role',
            'email',
            'password'
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
            'A' => 15,
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 25,
            'G' => 15,
        ];
    }
}
