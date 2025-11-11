<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Sin DBAL, usa SQL puro:
        if (Schema::hasColumn('vacancies', 'ciclo_requerido')) {
            DB::statement("ALTER TABLE vacancies MODIFY ciclo_requerido VARCHAR(255) NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vacancies', 'ciclo_requerido')) {
            DB::statement("ALTER TABLE vacancies MODIFY ciclo_requerido VARCHAR(255) NOT NULL");
        }
    }
};
