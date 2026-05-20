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
        Schema::create('wedding_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained()->cascadeOnDelete();
            $table->integer('no');
            $table->string('nama_tamu');
            $table->enum('pihak', ['cpw', 'cpp', 'umum'])->default('umum');
            $table->enum('status', ['belum_dikirim', 'sudah_dikirim', 'hadir', 'tidak_hadir'])->default('belum_dikirim');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_guests');
    }
};
