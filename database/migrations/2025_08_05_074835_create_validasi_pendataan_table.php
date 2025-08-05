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
        Schema::create('validasi_pendataan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('anggota_id')->constrained('anggota');
            $table->foreignId('pendataan_id')->constrained('pendataan');
            $table->string('jumlah')->nullable();
            $table->date('tanggal_validasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi_pendataan');
    }
};
