<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class JadwalAbsensiTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                'NIP001',
                '2024-01-15',
                '08:00',
                '17:00',
                '-6.200000',
                '106.816666',
                '100'
            ],
            [
                'NIP002',
                '2024-01-16',
                '08:30',
                '17:30',
                '-6.175110',
                '106.865036',
                '150'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nip',
            'tanggal',
            'jam_check_in',
            'jam_check_out',
            'latitude',
            'longitude',
            'radius_meter'
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
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
        ];
    }
}
