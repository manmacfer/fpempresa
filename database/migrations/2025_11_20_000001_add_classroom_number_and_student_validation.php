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
        // Agregar classroom_number único a classrooms
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('classroom_number', 50)->unique()->after('nombre');
        });

        // Agregar campo validated a students
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('validated')->default(false)->after('classroom_id');
        });
    }

    /**
     * Run the migrations down.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropUnique(['classroom_number']);
            $table->dropColumn('classroom_number');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('validated');
        });
    }
};
