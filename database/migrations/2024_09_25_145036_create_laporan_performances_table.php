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
        Schema::create('laporan_performances', function (Blueprint $table) {
            $table->id();
            $table->integer('lolos_sortir')->nullable();
            $table->integer('konfirmasi_hadir')->nullable();
            $table->integer('lolos')->nullable();
            $table->integer('training')->nullable();
            $table->integer('tandem')->nullable();
            $table->integer('PKM_baru')->nullable();
            $table->integer('PKM_batal_join')->nullable();
            $table->integer('resign')->nullable();
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->integer('posisi_id')->nullable();
            $table->integer('wilayah_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_performances');
    }
};
