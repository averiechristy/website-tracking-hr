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
        Schema::create('training_a_b_m_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kandidat_id')->nullable();
            $table->unsignedBigInteger('abm_id');
            $table->string('nama_kandidat')->nullable();
            $table->string('nama_abm')->nullable();
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
        Schema::dropIfExists('training_a_b_m_s');
    }
};
