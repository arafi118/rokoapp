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
        Schema::create('anggota', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->nullable();
            $table->string('nik')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('norek')->nullable();
            $table->string('agama')->nullable();
            $table->string('status')->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('ijazah')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('tahun_lulus')->nullable();
            $table->string('tinggi_badan')->nullable();
            $table->string('berat_badan')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
