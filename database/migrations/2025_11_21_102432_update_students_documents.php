<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Eliminar cv_path si existe
            if (Schema::hasColumn('students', 'cv_path')) {
                $table->dropColumn('cv_path');
            }
            
            // Renombrar other_cert_paths a other_certs_paths si existe la columna antigua
            if (Schema::hasColumn('students', 'other_cert_paths') && !Schema::hasColumn('students', 'other_certs_paths')) {
                $table->renameColumn('other_cert_paths', 'other_certs_paths');
            }
            
            // Crear other_certs_paths si no existe
            if (!Schema::hasColumn('students', 'other_certs_paths')) {
                $table->json('other_certs_paths')->nullable()->after('cover_letter_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Restaurar cv_path
            if (!Schema::hasColumn('students', 'cv_path')) {
                $table->string('cv_path')->nullable()->after('avatar_path');
            }
            
            // Renombrar de vuelta si es necesario
            if (Schema::hasColumn('students', 'other_certs_paths')) {
                $table->renameColumn('other_certs_paths', 'other_cert_paths');
            }
        });
    }
};
