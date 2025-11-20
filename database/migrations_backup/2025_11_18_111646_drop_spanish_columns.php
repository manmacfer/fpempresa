<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // students: eliminar ciclo, modalidad, ubicacion (si existen)
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'ciclo')) {
                    $table->dropColumn('ciclo');
                }
                if (Schema::hasColumn('students', 'modalidad')) {
                    $table->dropColumn('modalidad');
                }
                if (Schema::hasColumn('students', 'ubicacion')) {
                    $table->dropColumn('ubicacion');
                }
            });
        }

        // vacancies: eliminar ciclo_requerido, modalidad, ubicacion (si existen)
        if (Schema::hasTable('vacancies')) {
            Schema::table('vacancies', function (Blueprint $table) {
                if (Schema::hasColumn('vacancies', 'ciclo_requerido')) {
                    $table->dropColumn('ciclo_requerido');
                }
                if (Schema::hasColumn('vacancies', 'modalidad')) {
                    $table->dropColumn('modalidad');
                }
                if (Schema::hasColumn('vacancies', 'ubicacion')) {
                    $table->dropColumn('ubicacion');
                }
            });
        }

        // companies: eliminar nombre, ubicacion (si existen)
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (Schema::hasColumn('companies', 'nombre')) {
                    $table->dropColumn('nombre');
                }
                if (Schema::hasColumn('companies', 'ubicacion')) {
                    $table->dropColumn('ubicacion');
                }
            });
        }

        // languages: eliminar nombre, codigo (si existen)
        if (Schema::hasTable('languages')) {
            Schema::table('languages', function (Blueprint $table) {
                if (Schema::hasColumn('languages', 'nombre')) {
                    $table->dropColumn('nombre');
                }
                if (Schema::hasColumn('languages', 'codigo')) {
                    $table->dropColumn('codigo');
                }
            });
        }

        // competencies: eliminar nombre (si existe)
        if (Schema::hasTable('competencies')) {
            Schema::table('competencies', function (Blueprint $table) {
                if (Schema::hasColumn('competencies', 'nombre')) {
                    $table->dropColumn('nombre');
                }
            });
        }
    }

    public function down(): void
    {
        // Restaurar columnas como nullable strings (si quieres volver atrás)
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'ciclo')) {
                    $table->string('ciclo')->nullable();
                }
                if (! Schema::hasColumn('students', 'modalidad')) {
                    $table->string('modalidad')->nullable();
                }
                if (! Schema::hasColumn('students', 'ubicacion')) {
                    $table->string('ubicacion')->nullable();
                }
            });
        }

        if (Schema::hasTable('vacancies')) {
            Schema::table('vacancies', function (Blueprint $table) {
                if (! Schema::hasColumn('vacancies', 'ciclo_requerido')) {
                    $table->string('ciclo_requerido')->nullable();
                }
                if (! Schema::hasColumn('vacancies', 'modalidad')) {
                    $table->string('modalidad')->nullable();
                }
                if (! Schema::hasColumn('vacancies', 'ubicacion')) {
                    $table->string('ubicacion')->nullable();
                }
            });
        }

        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (! Schema::hasColumn('companies', 'nombre')) {
                    $table->string('nombre')->nullable();
                }
                if (! Schema::hasColumn('companies', 'ubicacion')) {
                    $table->string('ubicacion')->nullable();
                }
            });
        }

        if (Schema::hasTable('languages')) {
            Schema::table('languages', function (Blueprint $table) {
                if (! Schema::hasColumn('languages', 'nombre')) {
                    $table->string('nombre')->nullable();
                }
                if (! Schema::hasColumn('languages', 'codigo')) {
                    $table->string('codigo')->nullable();
                }
            });
        }

        if (Schema::hasTable('competencies')) {
            Schema::table('competencies', function (Blueprint $table) {
                if (! Schema::hasColumn('competencies', 'nombre')) {
                    $table->string('nombre')->nullable();
                }
            });
        }
    }
};