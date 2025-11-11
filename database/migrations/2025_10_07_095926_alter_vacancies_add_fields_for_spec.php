<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Crear columnas nuevas solo si no existen
        Schema::table('vacancies', function (Blueprint $table) {
            if (!Schema::hasColumn('vacancies', 'ciclo_formativo_req')) {
                $table->string('ciclo_formativo_req')->nullable()->after('title');
            }
            if (!Schema::hasColumn('vacancies', 'horarios_disponibles')) {
                $table->json('horarios_disponibles')->nullable()->after('ubicacion');
            }
            if (!Schema::hasColumn('vacancies', 'requiere_carnet')) {
                $table->boolean('requiere_carnet')->default(false)->after('horarios_disponibles');
            }
            if (!Schema::hasColumn('vacancies', 'permite_teletrabajo')) {
                $table->boolean('permite_teletrabajo')->default(false)->after('requiere_carnet');
            }
            if (!Schema::hasColumn('vacancies', 'estado_vacante')) {
                $table->enum('estado_vacante', ['ABIERTA', 'SELECCION', 'CERRADA'])
                      ->default('ABIERTA')->after('permite_teletrabajo');
            }
        });

        // 2) Backfill (solo si existen las columnas de origen/destino)
        if (Schema::hasColumn('vacancies', 'ciclo_formativo_req') && Schema::hasColumn('vacancies', 'ciclo_requerido')) {
            DB::table('vacancies')
                ->whereNull('ciclo_formativo_req')
                ->whereNotNull('ciclo_requerido')
                ->update(['ciclo_formativo_req' => DB::raw('ciclo_requerido')]);
        }

        if (Schema::hasColumn('vacancies', 'estado_vacante') && Schema::hasColumn('vacancies', 'status')) {
            DB::table('vacancies')
                ->whereNull('estado_vacante')
                ->whereNotNull('status')
                ->update(['estado_vacante' => DB::raw('UPPER(status)')]);
        }

        // 3) Normalizar 'modalidad' y convertir a ENUM solo si la columna existe
        if (Schema::hasColumn('vacancies', 'modalidad')) {
            // Normalización previa para evitar truncations
            DB::statement("UPDATE vacancies SET modalidad = UPPER(TRIM(modalidad)) WHERE modalidad IS NOT NULL");

            DB::table('vacancies')->whereIn('modalidad', ['HÍBRIDA','HÍBRIDO','HIBRIDO','MIXTA','HYBRID'])
                ->update(['modalidad' => 'HIBRIDA']);

            DB::table('vacancies')->whereIn('modalidad', ['REMOTO','REMOTE','TELETRABAJO','TELEWORK'])
                ->update(['modalidad' => 'REMOTA']);

            DB::table('vacancies')->whereIn('modalidad', ['ON-SITE','ONSITE','OFICINA','OFFICE','PRESENTIAL'])
                ->update(['modalidad' => 'PRESENCIAL']);

            DB::table('vacancies')
                ->whereNull('modalidad')
                ->orWhere('modalidad', '')
                ->orWhereNotIn('modalidad', ['PRESENCIAL','HIBRIDA','REMOTA'])
                ->update(['modalidad' => 'PRESENCIAL']);

            // Convertir a ENUM (si ya lo es con esos valores, este ALTER no cambia nada)
            DB::statement("
                ALTER TABLE vacancies
                MODIFY COLUMN modalidad ENUM('PRESENCIAL','HIBRIDA','REMOTA') NOT NULL DEFAULT 'PRESENCIAL'
            ");
        }
    }

    public function down(): void
    {
        // Elimina solo si existen (para que sea reversible sin errores)
        Schema::table('vacancies', function (Blueprint $table) {
            if (Schema::hasColumn('vacancies', 'ciclo_formativo_req')) {
                $table->dropColumn('ciclo_formativo_req');
            }
            if (Schema::hasColumn('vacancies', 'horarios_disponibles')) {
                $table->dropColumn('horarios_disponibles');
            }
            if (Schema::hasColumn('vacancies', 'requiere_carnet')) {
                $table->dropColumn('requiere_carnet');
            }
            if (Schema::hasColumn('vacancies', 'permite_teletrabajo')) {
                $table->dropColumn('permite_teletrabajo');
            }
            if (Schema::hasColumn('vacancies', 'estado_vacante')) {
                $table->dropColumn('estado_vacante');
            }
        });

        // Devolver modalidad a VARCHAR si existe
        if (Schema::hasColumn('vacancies', 'modalidad')) {
            DB::statement("ALTER TABLE vacancies MODIFY COLUMN modalidad VARCHAR(20) NOT NULL");
        }
    }
};
