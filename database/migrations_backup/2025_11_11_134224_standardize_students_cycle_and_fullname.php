<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Renombrar 'ciclo' a 'cycle' si existe
        if (Schema::hasColumn('students', 'ciclo') && !Schema::hasColumn('students', 'cycle')) {
            Schema::table('students', function (Blueprint $table) {
                $table->renameColumn('ciclo', 'cycle');
            });
        }

        // Asegurar que 'cycle' existe (por si la tabla venía sin él)
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'cycle')) {
                $table->string('cycle', 20)->nullable()->after('user_id');
            }
        });

        // Hacer 'full_name' nullable (ya que derivamos de users)
        if (Schema::hasColumn('students', 'full_name')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('full_name', 255)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Revertir 'cycle' a 'ciclo' solo si originalmente existía
        if (Schema::hasColumn('students', 'cycle') && !Schema::hasColumn('students', 'ciclo')) {
            Schema::table('students', function (Blueprint $table) {
                $table->renameColumn('cycle', 'ciclo');
            });
        }

        // 'full_name' volver a NOT NULL (no imprescindible para el down)
        if (Schema::hasColumn('students', 'full_name')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('full_name', 255)->nullable(false)->change();
            });
        }
    }
};
