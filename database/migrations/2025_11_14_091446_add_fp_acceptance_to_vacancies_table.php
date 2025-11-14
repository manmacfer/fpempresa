<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            // Justo detrás del ciclo requerido, por orden lógico
            $table->boolean('accepts_fp_general')
                ->default(true)
                ->after('cycle_required');

            $table->boolean('accepts_fp_dual')
                ->default(true)
                ->after('accepts_fp_general');
        });
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn(['accepts_fp_general', 'accepts_fp_dual']);
        });
    }
};
