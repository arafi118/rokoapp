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
        Schema::create('validasi_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->constrained('produksi');
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->foreignId('mandor')->constrained('anggota');
            $table->string('jumlah_baik')->nullable();
            $table->string('jumlah_buruk')->nullable();
            $table->string('jumlah_buruk2')->nullable();
            $table->date('tanggal_validasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi_produksi');
    }
};
