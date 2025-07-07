<?php

// File: app/Imports/JadwalAbsensiImport.php
namespace App\Imports;

use App\Models\JadwalAbsensiManpower;
use App\Models\Manpower;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Carbon\Carbon;

class JadwalAbsensiImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Cari manpower berdasarkan NIP
                $manpower = Manpower::where('nip', $row['nip'])->first();
                if (!$manpower) {
                    $this->errors[] = "NIP '{$row['nip']}' tidak ditemukan pada baris " . ($row->keys()->first() + 2);
                    continue;
                }

                // Parse tanggal
                $tanggal = $this->parseDate($row['tanggal']);
                if (!$tanggal) {
                    $this->errors[] = "Format tanggal tidak valid pada baris " . ($row->keys()->first() + 2);
                    continue;
                }

                JadwalAbsensiManpower::create([
                    'manpower_id' => $manpower->id,
                    'tanggal' => $tanggal,
                    'jam_check_in' => $row['jam_check_in'],
                    'jam_check_out' => $row['jam_check_out'],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'radius_meter' => $row['radius_meter'] ?? 100,
                ]);
            } catch (\Exception $e) {
                $this->errors[] = "Error pada baris " . ($row->keys()->first() + 2) . ": " . $e->getMessage();
            }
        }
    }

    private function parseDate($dateString)
    {
        try {
            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'];
            
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nip' => 'required|string',
            'tanggal' => 'required',
            'jam_check_in' => 'required|string',
            'jam_check_out' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'nullable|numeric|min:1',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jam_check_in.required' => 'Jam check in wajib diisi',
            'jam_check_out.required' => 'Jam check out wajib diisi',
            'latitude.required' => 'Latitude wajib diisi',
            'latitude.numeric' => 'Latitude harus berupa angka',
            'longitude.required' => 'Longitude wajib diisi',
            'longitude.numeric' => 'Longitude harus berupa angka',
            'radius_meter.numeric' => 'Radius harus berupa angka',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
