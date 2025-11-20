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
        Schema::table('rejections', function (Blueprint $table) {
            // Eliminar las columnas antiguas
            $table->dropForeign(['application_id']);
            $table->dropUnique(['application_id']);
            $table->dropColumn('application_id');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            
            // Agregar las nuevas columnas
            $table->foreignId('vacancy_id')->after('id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('student_id')->after('vacancy_id')->constrained('students')->cascadeOnDelete();
            
            // Agregar índice único
            $table->unique(['vacancy_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rejections', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropForeign(['vacancy_id']);
            $table->dropForeign(['student_id']);
            $table->dropUnique(['vacancy_id', 'student_id']);
            $table->dropColumn(['vacancy_id', 'student_id']);
            
            // Restaurar columnas antiguas
            $table->foreignId('application_id')->after('id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('created_by')->after('reason')->constrained('users')->cascadeOnDelete();
            $table->unique('application_id');
        });
    }
};
