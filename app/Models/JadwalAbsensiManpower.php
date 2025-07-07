<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalAbsensiManpower extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'jam_check_in',
        'jam_check_out',
        'latitude',
        'longitude',
        'radius_meter',
        'manpower_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function manpower()
    {
        return $this->belongsTo(Manpower::class);
    }
}