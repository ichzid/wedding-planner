<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LegacyWeddingDataSeeder extends Seeder
{
    private const TABLES = [
        'users',
        'weddings',
        'wedding_budget',
        'seserahan_list',
        'wedding_checklist',
        'kua_documents',
        'wedding_guests',
    ];

    public function run(): void
    {
        $dumpPath = base_path('wedding-planner.sql');

        if (! is_file($dumpPath)) {
            throw new RuntimeException("File dump tidak ditemukan: {$dumpPath}");
        }

        $sql = file_get_contents($dumpPath);

        if ($sql === false) {
            throw new RuntimeException("File dump tidak dapat dibaca: {$dumpPath}");
        }

        $statements = $this->extractInsertStatements($sql);

        Schema::disableForeignKeyConstraints();

        try {
            foreach (array_reverse(self::TABLES) as $table) {
                DB::table($table)->truncate();
            }

            foreach (self::TABLES as $table) {
                if (isset($statements[$table])) {
                    DB::unprepared($statements[$table]);
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    private function extractInsertStatements(string $sql): array
    {
        $tables = implode('|', array_map('preg_quote', self::TABLES));
        $pattern = '/INSERT INTO `(' . $tables . ')`\s*\([^;]+?;/s';

        preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER);

        $statements = [];

        foreach ($matches as $match) {
            $statements[$match[1]] = $match[0];
        }

        $missingTables = array_diff(self::TABLES, array_keys($statements));

        if ($missingTables !== []) {
            throw new RuntimeException('Data INSERT tidak ditemukan untuk tabel: ' . implode(', ', $missingTables));
        }

        return $statements;
    }
}
