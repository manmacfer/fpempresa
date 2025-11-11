<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('company', 190);
            $table->string('position', 190)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('functions')->nullable(); // funciones principales
            $table->boolean('is_non_formal')->default(false); // experiencias no formales/colaboraciones
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('student_experiences');
    }
};
