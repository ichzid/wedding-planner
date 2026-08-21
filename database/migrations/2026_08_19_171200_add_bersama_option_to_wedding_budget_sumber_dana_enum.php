<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE wedding_budget MODIFY sumber_dana ENUM('cpp', 'cpw', 'bersama') NOT NULL DEFAULT 'cpp'");
    }

    public function down(): void
    {
        DB::statement("UPDATE wedding_budget SET sumber_dana = 'cpp' WHERE sumber_dana = 'bersama'");
        DB::statement("ALTER TABLE wedding_budget MODIFY sumber_dana ENUM('cpp', 'cpw') NOT NULL DEFAULT 'cpp'");
    }
};
