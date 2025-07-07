<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absensi_manpowers', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('jam_check_in')->nullable();
            $table->time('jam_check_out')->nullable();
            $table->decimal('latitude_check_in', 10, 8)->nullable();
            $table->decimal('longitude_check_in', 11, 8)->nullable();
            $table->decimal('latitude_check_out', 10, 8)->nullable();
            $table->decimal('longitude_check_out', 11, 8)->nullable();
            $table->foreignId('manpower_id')->constrained('manpowers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi_manpowers');
    }
};