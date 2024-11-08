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
        Schema::create('target_jumlahs', function (Blueprint $table) {
            $table->id();
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->integer('target_mpp')->nullable();
            $table->integer('jumlah_mitra')->nullable();
            $table->integer('target_join')->nullable();
            $table->unsignedBigInteger('posisi_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_jumlahs');
    }
};
