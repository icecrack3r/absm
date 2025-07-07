<?php

// File: app/Imports/ManpowerImport.php
namespace App\Imports;

use App\Models\Manpower;
use App\Models\Projek;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ManpowerImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Cari projek berdasarkan kode_projek
                $projek = Projek::where('kode_projek', $row['kode_projek'])->first();
                if (!$projek) {
                    $this->errors[] = "Kode projek '{$row['kode_projek']}' tidak ditemukan pada baris " . ($row->keys()->first() + 2);
                    continue;
                }

                // Cari role berdasarkan nama_role
                $role = Role::where('nama_role', $row['nama_role'])->first();
                if (!$role) {
                    $this->errors[] = "Role '{$row['nama_role']}' tidak ditemukan pada baris " . ($row->keys()->first() + 2);
                    continue;
                }

                Manpower::create([
                    'nip' => $row['nip'],
                    'nama_lengkap' => $row['nama_lengkap'],
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin']),
                    'projek_id' => $projek->id,
                    'kode_projek' => $projek->kode_projek,
                    'role_id' => $role->id,
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                ]);
            } catch (\Exception $e) {
                $this->errors[] = "Error pada baris " . ($row->keys()->first() + 2) . ": " . $e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [
            'nip' => 'required|unique:manpowers,nip',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P,l,p',
            'kode_projek' => 'required|string',
            'nama_role' => 'required|string',
            'email' => 'required|email|unique:manpowers,email',
            'password' => 'required|string|min:6',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
