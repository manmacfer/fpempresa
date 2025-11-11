<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('vacancies', 'ciclo_requerido')) {
            Schema::table('vacancies', function (Blueprint $table) {
                $table->dropColumn('ciclo_requerido');
            });
        }
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->string('ciclo_requerido')->nullable(); 
        });
    }
};
