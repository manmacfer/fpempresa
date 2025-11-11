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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // “ES”, “EN”, “FR”
            $table->string('nombre');           // “Español”, “Inglés”, “Francés”
            $table->timestamps();
        });

        // Alumno ↔ Idioma (nivel alcanzado)
        Schema::create('alumno_idioma', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->enum('nivel', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->default('A2');
            $table->primary(['student_id', 'language_id']);
        });

        // Vacante ↔ Idioma requerido (nivel mínimo)
        Schema::create('vacante_idioma_req', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->enum('nivel_minimo', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->default('A2');
            $table->primary(['vacancy_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacante_idioma_req');
        Schema::dropIfExists('alumno_idioma');
        Schema::dropIfExists('languages');
    }
};
