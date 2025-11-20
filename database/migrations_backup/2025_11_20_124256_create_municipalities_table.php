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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique()->comment('Código INE del municipio');
            $table->string('name', 150)->comment('Nombre del municipio');
            $table->string('province_code', 2)->index()->comment('Código de provincia (primeros 2 dígitos del código INE)');
            $table->string('province_name', 100)->index()->comment('Nombre de la provincia');
            $table->timestamps();
            
            // Índice compuesto para búsquedas rápidas
            $table->index(['name', 'province_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
