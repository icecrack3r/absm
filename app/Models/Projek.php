<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projek extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_projek',
        'logo_projek',
        'kode_projek',
        'nama_lengkap_pic',
    ];

    public function manpowers()
    {
        return $this->hasMany(Manpower::class);
    }
}