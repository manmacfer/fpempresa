<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matchings', function (Blueprint $table) {
            // Primero, eliminar las foreign keys que dependen del índice
            $table->dropForeign(['student_id']);
            $table->dropForeign(['company_id']);
        });

        Schema::table('matchings', function (Blueprint $table) {
            // Ahora podemos eliminar el constraint único
            $table->dropUnique('matchings_student_id_company_id_unique');
        });

        Schema::table('matchings', function (Blueprint $table) {
            // Recrear las foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            // Añadir nuevo constraint único que incluye vacancy_id
            $table->unique(['student_id', 'vacancy_id'], 'matchings_student_vacancy_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matchings', function (Blueprint $table) {
            // Eliminar las foreign keys
            $table->dropForeign(['student_id']);
            $table->dropForeign(['company_id']);
        });

        Schema::table('matchings', function (Blueprint $table) {
            // Eliminar el constraint único nuevo
            $table->dropUnique('matchings_student_vacancy_unique');
        });

        Schema::table('matchings', function (Blueprint $table) {
            // Recrear las foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            // Restaurar el constraint anterior
            $table->unique(['student_id', 'company_id'], 'matchings_student_id_company_id_unique');
        });
    }
};
