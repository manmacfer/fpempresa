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
        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();      // p.ej. "laravel", "vue", "git"
            $table->string('nombre');              // “Laravel”, “Vue”, “Git”
            $table->timestamps();
        });

        // Alumno ↔ Competencia (qué competencias tiene)
        Schema::create('alumno_competencia', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->primary(['student_id', 'competency_id']);
        });

        // Vacante ↔ Competencia requerida (y si es obligatoria)
        Schema::create('vacante_competencia_req', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->boolean('es_obligatoria')->default(true);
            $table->primary(['vacancy_id', 'competency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacante_competencia_req');
        Schema::dropIfExists('alumno_competencia');
        Schema::dropIfExists('competencies');
    }
};
