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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // cada profe es un User con role=TUTOR/PROFESOR
            $table->string('full_name');
            $table->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // p.ej. “2º DAW A”
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('user_id')->constrained('classrooms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('classroom_id');
        });
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('teachers');
    }
};
