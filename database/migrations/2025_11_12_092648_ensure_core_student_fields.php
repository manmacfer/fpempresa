<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('students')) return;

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('students', 'last_name')) {
                $table->string('last_name', 150)->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('students', 'cycle')) {
                $table->string('cycle', 20)->nullable()->after('last_name');
            }
        });

        // Intenta rellenar desde users.name si estuvieran vacíos
        try {
            $rows = DB::select("
                SELECT s.user_id, u.name
                FROM students s
                JOIN users u ON u.id = s.user_id
                WHERE (s.first_name IS NULL OR s.first_name = '')
                  AND (u.name IS NOT NULL AND u.name <> '')
            ");
            foreach ($rows as $r) {
                $parts = preg_split('/\s+/', trim($r->name), 2);
                DB::table('students')->where('user_id', $r->user_id)->update([
                    'first_name' => $parts[0] ?? $r->name,
                    'last_name'  => $parts[1] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            // no-op
        }
    }

    public function down(): void
    {
        // No quitamos columnas para no perder datos
    }
};
