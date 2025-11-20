<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vacancies')) {
            Schema::create('vacancies', function (Blueprint $table) {
                $table->id();

                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

                $table->string('title');
                $table->text('description')->nullable();

                // Requisitos para matching
                $table->string('cycle_required', 50)->nullable();      // ej: 1DAM, 2DAW...
                $table->string('mode', 20)->nullable();                // onsite | remote | hybrid
                $table->string('city', 100)->nullable();
                $table->string('province', 100)->nullable();
                $table->unsignedTinyInteger('hours_per_week')->nullable();
                $table->unsignedSmallInteger('duration_weeks')->nullable();

                $table->boolean('paid')->default(false);
                $table->unsignedInteger('salary_month')->nullable();

                $table->json('tech_stack')->nullable(); // ej: ["Laravel","Vue","MySQL"]
                $table->json('soft_skills')->nullable(); // ej: ["Trabajo en equipo","Comunicación"]

                $table->string('status', 20)->default('draft'); // draft | published | closed
                $table->timestamp('published_at')->nullable();

                $table->timestamps();

                $table->index(['company_id','status']);
                $table->index(['cycle_required','mode']);
                $table->index(['city','province']);
            });

            return;
        }

        // Si la tabla existe, añadimos lo que falte
        Schema::table('vacancies', function (Blueprint $table) {
            if (!Schema::hasColumn('vacancies', 'company_id')) {
                $table->foreignId('company_id')->after('id')->constrained('companies')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('vacancies', 'title')) {
                $table->string('title')->after('company_id');
            }
            if (!Schema::hasColumn('vacancies', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('vacancies', 'cycle_required')) {
                $table->string('cycle_required', 50)->nullable()->after('description');
            }
            if (!Schema::hasColumn('vacancies', 'mode')) {
                $table->string('mode', 20)->nullable()->after('cycle_required');
            }
            if (!Schema::hasColumn('vacancies', 'city')) {
                $table->string('city', 100)->nullable()->after('mode');
            }
            if (!Schema::hasColumn('vacancies', 'province')) {
                $table->string('province', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('vacancies', 'hours_per_week')) {
                $table->unsignedTinyInteger('hours_per_week')->nullable()->after('province');
            }
            if (!Schema::hasColumn('vacancies', 'duration_weeks')) {
                $table->unsignedSmallInteger('duration_weeks')->nullable()->after('hours_per_week');
            }
            if (!Schema::hasColumn('vacancies', 'paid')) {
                $table->boolean('paid')->default(false)->after('duration_weeks');
            }
            if (!Schema::hasColumn('vacancies', 'salary_month')) {
                $table->unsignedInteger('salary_month')->nullable()->after('paid');
            }
            if (!Schema::hasColumn('vacancies', 'tech_stack')) {
                $table->json('tech_stack')->nullable()->after('salary_month');
            }
            if (!Schema::hasColumn('vacancies', 'soft_skills')) {
                $table->json('soft_skills')->nullable()->after('tech_stack');
            }
            if (!Schema::hasColumn('vacancies', 'status')) {
                $table->string('status', 20)->default('draft')->after('soft_skills');
            }
            if (!Schema::hasColumn('vacancies', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }
            // indexes: los creamos solo si no existen
            // (Laravel no tiene API para comprobar índices fácilmente sin DBAL; lo dejamos así)
        });
    }

    public function down(): void
    {
        // No borramos columnas para no romper datos.
        // Si quieres revertir, comenta esto y usa dropIfExists:
        // Schema::dropIfExists('vacancies');
    }
};
