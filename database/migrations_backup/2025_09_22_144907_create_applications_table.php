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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('state', ['enviada', 'vista', 'entrevista', 'rechazada', 'aceptada'])->default('enviada');
            $table->json('feedback')->nullable();
            $table->timestamps();
            $table->unique(['vacancy_id', 'student_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
