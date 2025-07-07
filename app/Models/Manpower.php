<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Manpower extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'kode_projek',
        'email',
        'password',
        'projek_id',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'jenis_kelamin' => 'string', // or enum if applicable
        'is_active' => 'boolean' // if you have status flags
    ];

    public function projek()
    {
        return $this->belongsTo(Projek::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiManpower::class);
    }

    public function jadwalAbsensi()
    {
        return $this->hasMany(JadwalAbsensiManpower::class);
    }
}