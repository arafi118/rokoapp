<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi_anggota', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('anggota_kelompok_id')->constrained('anggota_kelompok');
            $table->foreignId('absensi_id')->constrained('absensi');
            $table->time('jam')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_anggota');
    }
};
