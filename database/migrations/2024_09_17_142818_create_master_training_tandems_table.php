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
        Schema::create('master_training_tandems', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan')->nullable();
            $table->string('posisi')->nullable();
            $table->string('domisili')->nullable();
            $table->string('kelas_training')->nullable();
            $table->string('tanggal_training')->nullable();
            $table->string('status')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_training_tandems');
    }
};
