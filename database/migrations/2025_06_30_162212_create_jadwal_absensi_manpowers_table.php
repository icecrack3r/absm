<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jadwal_absensi_manpowers', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('jam_check_in');
            $table->time('jam_check_out');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meter')->default(100);
            $table->foreignId('manpower_id')->constrained('manpowers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_absensi_manpowers');
    }
};