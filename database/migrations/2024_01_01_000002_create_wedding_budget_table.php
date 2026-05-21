<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wedding_budget', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained('weddings')->onDelete('cascade');
            $table->integer('no')->default(0);
            $table->string('kategori');
            $table->string('item');
            $table->string('vendor')->nullable();
            $table->bigInteger('estimasi_budget')->default(0);
            $table->bigInteger('dp')->default(0);
            $table->bigInteger('pelunasan')->default(0);
            $table->enum('sumber_dana', ['cpp', 'cpw'])->default('cpp');
            $table->enum('status', ['belum', 'dp_terbayar', 'lunas'])->default('belum');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_budget');
    }
};
