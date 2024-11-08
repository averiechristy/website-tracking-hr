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
        Schema::create('master_konfirms', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal')->nullable();
            $table->string('nama_sourcing')->nullable();
            $table->unsignedBigInteger('sourcing_id')->nullable();
            $table->unsignedBigInteger('posisi_id')->nullable();
            $table->unsignedBigInteger('wilayah_id')->nullable();
            $table->string('nama_posisi')->nullable();
            $table->string('nama_wilayah')->nullable();
            $table->integer('jumlah_undang_otomatis')->nullable();
            $table->integer('jumlah_konfirm_manual')->nullable();
            $table->string('keterangan')->nullable();
            $table->integer('day')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_konfirms');
    }

};
