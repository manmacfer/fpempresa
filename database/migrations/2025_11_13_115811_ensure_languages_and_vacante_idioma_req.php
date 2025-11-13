<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // === 1) LANGUAGES ===
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 10)->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('languages', function (Blueprint $table) {
                if (!Schema::hasColumn('languages', 'name')) {
                    $table->string('name', 100)->nullable();
                }
                if (!Schema::hasColumn('languages', 'code')) {
                    $table->string('code', 10)->nullable();
                }
                $hasCreated = Schema::hasColumn('languages', 'created_at');
                $hasUpdated = Schema::hasColumn('languages', 'updated_at');
                if (!$hasCreated && !$hasUpdated) {
                    $table->timestamps();
                } else {
                    if (!$hasCreated) $table->timestamp('created_at')->nullable();
                    if (!$hasUpdated) $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // === 2) VACANTE_IDIOMA_REQ ===
        if (!Schema::hasTable('vacante_idioma_req')) {
            Schema::create('vacante_idioma_req', function (Blueprint $table) {
                $table->id(); // sólo al crear la tabla
                $table->unsignedBigInteger('vacancy_id');
                $table->unsignedBigInteger('language_id');
                $table->enum('min_level', ['A1','A2','B1','B2','C1','C2'])->nullable();
                $table->boolean('required')->default(true);
                $table->timestamps();

                // Índice único combinado
                $table->unique(['vacancy_id','language_id'], 'vac_lang_unique');

                // FKs (sólo al crear)
                $table->foreign('vacancy_id')->references('id')->on('vacancies')->cascadeOnDelete();
                $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
            });
        } else {
            // La tabla ya existe: NO tocar PK, índices ni FKs (evitamos duplicados)
            Schema::table('vacante_idioma_req', function (Blueprint $table) {
                if (!Schema::hasColumn('vacante_idioma_req', 'vacancy_id')) {
                    $table->unsignedBigInteger('vacancy_id');
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'language_id')) {
                    $table->unsignedBigInteger('language_id');
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'min_level')) {
                    $table->enum('min_level', ['A1','A2','B1','B2','C1','C2'])->nullable();
                }
                if (!Schema::hasColumn('vacante_idioma_req', 'required')) {
                    $table->boolean('required')->default(true);
                }

                $hasCreated = Schema::hasColumn('vacante_idioma_req', 'created_at');
                $hasUpdated = Schema::hasColumn('vacante_idioma_req', 'updated_at');
                if (!$hasCreated && !$hasUpdated) {
                    $table->timestamps();
                } else {
                    if (!$hasCreated) $table->timestamp('created_at')->nullable();
                    if (!$hasUpdated) $table->timestamp('updated_at')->nullable();
                }

                // OJO: aquí NO añadimos índices ni FKs.
            });
        }
    }

    public function down(): void
    {
        // No hacemos drop para no perder datos.
    }
};
