<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('status', ['ACTIVO','CERRADO'])->default('ACTIVO');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->unique(['vacancy_id','student_id']); // no duplicar el mismo match
            // Nota: la regla "solo un match ACTIVO por alumno y por vacante" la haremos por código (MySQL no tiene partial indexes).
            $table->index(['vacancy_id','status']);
            $table->index(['student_id','status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('matches');
    }
};
