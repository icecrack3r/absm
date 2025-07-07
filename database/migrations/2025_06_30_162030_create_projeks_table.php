<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projeks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_projek');
            $table->string('logo_projek')->nullable();
            $table->string('kode_projek')->unique();
            $table->string('nama_lengkap_pic');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projeks');
    }
};