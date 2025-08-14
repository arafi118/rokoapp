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
        Schema::create('absensi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->string('jadwal')->nullable();
            $table->date('tanggal')->nullable();
            $table->time('jam')->nullable();
            $table->enum('status', ['H', 'S', 'I', 'A', 'T'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
