<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seserahan_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained('weddings')->onDelete('cascade');
            $table->integer('no')->default(0);
            $table->string('kategori');
            $table->string('nama_item');
            $table->enum('untuk', ['cpp', 'cpw'])->default('cpp');
            $table->integer('qty')->default(1);
            $table->string('satuan')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->enum('status', ['belum', 'sudah_dibeli'])->default('belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seserahan_list');
    }
};
