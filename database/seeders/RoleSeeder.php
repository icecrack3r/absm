<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nama_role' => 'Admin',
                'deskripsi' => 'Administrator sistem',
            ],
            [
                'nama_role' => 'Manager',
                'deskripsi' => 'Manager projek',
            ],
            [
                'nama_role' => 'Supervisor',
                'deskripsi' => 'Supervisor lapangan',
            ],
            [
                'nama_role' => 'Staff',
                'deskripsi' => 'Staff operasional',
            ],
            [
                'nama_role' => 'Operator',
                'deskripsi' => 'Operator mesin',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
