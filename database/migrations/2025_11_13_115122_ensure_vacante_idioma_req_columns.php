<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vacante_idioma_req')) {
            Schema::create('vacante_idioma_req', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
                $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
                $table->enum('min_level', ['A1','A2','B1','B2','C1','C2'])->nullable();
                $table->boolean('required')->default(true);
                $table->timestamps();
                $table->unique(['vacancy_id','language_id']);
            });
        } else {
            Schema::table('vacante_idioma_req', function (Blueprint $table) {
                if (!Schema::hasColumn('vacante_idioma_req', 'id')) {
                    $table->id()->first();
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'vacancy_id')) {
                    $table->foreignId('vacancy_id')->after('id')->constrained('vacancies')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'language_id')) {
                    $table->foreignId('language_id')->after('vacancy_id')->constrained('languages')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'min_level')) {
                    $table->enum('min_level', ['A1','A2','B1','B2','C1','C2'])->nullable()->after('language_id');
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'required')) {
                    $table->boolean('required')->default(true)->after('min_level');
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        // No bajamos tabla para no perder datos; si quieres:
        // Schema::dropIfExists('vacante_idioma_req');
    }
};
