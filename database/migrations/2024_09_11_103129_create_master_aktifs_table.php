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
        Schema::create('master_aktifs', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_id')->nullable();
            $table->string('nama_karyawan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('bulan')->nullable();
            $table->string('tahun')->nullable();
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_aktifs');
    }
};
