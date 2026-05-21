<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE seserahan_list MODIFY untuk ENUM('groom', 'bride', 'cpp', 'cpw') NOT NULL DEFAULT 'cpp'");
        DB::table('seserahan_list')->where('untuk', 'groom')->update(['untuk' => 'cpp']);
        DB::table('seserahan_list')->where('untuk', 'bride')->update(['untuk' => 'cpw']);
        DB::statement("ALTER TABLE seserahan_list MODIFY untuk ENUM('cpp', 'cpw') NOT NULL DEFAULT 'cpp'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE seserahan_list MODIFY untuk ENUM('groom', 'bride', 'cpp', 'cpw') NOT NULL DEFAULT 'groom'");
        DB::table('seserahan_list')->where('untuk', 'cpp')->update(['untuk' => 'groom']);
        DB::table('seserahan_list')->where('untuk', 'cpw')->update(['untuk' => 'bride']);
        DB::statement("ALTER TABLE seserahan_list MODIFY untuk ENUM('groom', 'bride') NOT NULL DEFAULT 'groom'");
    }
};
