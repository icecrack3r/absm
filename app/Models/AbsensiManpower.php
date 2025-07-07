<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiManpower extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'jam_check_in',
        'jam_check_out',
        'latitude_check_in',
        'longitude_check_in',
        'latitude_check_out',
        'longitude_check_out',
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