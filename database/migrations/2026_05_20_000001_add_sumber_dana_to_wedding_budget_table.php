<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('wedding_budget', 'sumber_dana')) {
            Schema::table('wedding_budget', function (Blueprint $table) {
                $table->enum('sumber_dana', ['cpp', 'cpw'])
                      ->default('cpp')
                      ->after('pelunasan');
            });
        }
    }

    public function down(): void
    {
        Schema::table('wedding_budget', function (Blueprint $table) {
            $table->dropColumn('sumber_dana');
        });
    }
};
