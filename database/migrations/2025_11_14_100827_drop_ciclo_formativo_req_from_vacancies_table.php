<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            if (Schema::hasColumn('vacancies', 'ciclo_formativo_req')) {
                $table->dropColumn('ciclo_formativo_req');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            // Por si quieres volver atrás
            if (! Schema::hasColumn('vacancies', 'ciclo_formativo_req')) {
                $table->string('ciclo_formativo_req', 190)->nullable()->after('title');
            }
        });
    }
};
