<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kua_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wedding_id')->constrained('weddings')->onDelete('cascade');
            $table->integer('no')->default(0);
            $table->string('nama_dokumen');
            $table->boolean('cpw_status')->default(false);
            $table->boolean('cpp_status')->default(false);
            $table->bigInteger('biaya')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kua_documents');
    }
};
