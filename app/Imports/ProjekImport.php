<?php

// File: app/Imports/ManpowerImport.php
// File: app/Imports/ProjekImport.php
namespace App\Imports;

use App\Models\Projek;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ProjekImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                Projek::create([
                    'nama_projek' => $row['nama_projek'],
                    'kode_projek' => $row['kode_projek'],
                    'nama_lengkap_pic' => $row['nama_lengkap_pic'],
                    'logo_projek' => null, // Logo tidak bisa diimport via Excel
                ]);
            } catch (\Exception $e) {
                $this->errors[] = "Error pada baris " . ($row->keys()->first() + 2) . ": " . $e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [
            'nama_projek' => 'required|string|max:255',
            'kode_projek' => 'required|string|max:255|unique:projeks,kode_projek',
            'nama_lengkap_pic' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_projek.required' => 'Nama projek wajib diisi',
            'kode_projek.required' => 'Kode projek wajib diisi',
            'kode_projek.unique' => 'Kode projek sudah terdaftar',
            'nama_lengkap_pic.required' => 'Nama PIC wajib diisi',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}