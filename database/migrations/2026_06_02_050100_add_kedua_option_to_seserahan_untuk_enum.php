<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE seserahan_list MODIFY untuk ENUM('cpp', 'cpw', 'kedua') NOT NULL DEFAULT 'cpp'");
    }

    public function down(): void
    {
        DB::statement("UPDATE seserahan_list SET untuk = 'cpp' WHERE untuk = 'kedua'");
        DB::statement("ALTER TABLE seserahan_list MODIFY untuk ENUM('cpp', 'cpw') NOT NULL DEFAULT 'cpp'");
    }
};
