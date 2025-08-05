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
        Schema::create('pendataan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('anggota_kelompok_id')->constrained('anggota_kelompok');
            $table->string('jumlah')->nullable();
            $table->date('tanggal_input')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendataan');
    }
};
