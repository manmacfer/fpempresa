<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('companies')) {
            return;
        }

        // 1) Si NO existe 'name', la creamos como nullable
        if (!Schema::hasColumn('companies', 'name')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('name')->nullable()->after('user_id');
            });
        } else {
            // 2) Si existe pero es NOT NULL, la ponemos NULL (sin requerir doctrine/dbal)
            try {
                DB::statement("ALTER TABLE companies MODIFY name VARCHAR(255) NULL");
            } catch (\Throwable $e) {
                // Si tu motor no acepta MODIFY, al menos lo dejamos como está
            }
        }

        // 3) Relleno de cortesía: si name está a NULL, usamos trade_name o legal_name
        try {
            DB::statement("UPDATE companies SET name = COALESCE(name, trade_name, legal_name) WHERE name IS NULL");
        } catch (\Throwable $e) {
            // Ignorar si alguna columna no existe aún
        }
    }

    public function down(): void
    {
        // No revertimos a NOT NULL para no romper inserciones futuras
        // (si quisieras, aquí podrías volver a NOT NULL con un default)
    }
};
