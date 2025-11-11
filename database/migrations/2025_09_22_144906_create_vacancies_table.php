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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('ciclo_requerido');               // p.ej. DAW
            $table->string('modalidad')->default('presencial'); // remoto/híbrido/presencial
            $table->string('ubicacion')->nullable();
            $table->json('idiomas')->nullable();             // ["EN:B1","ES:nativo"]
            $table->json('competencies')->nullable();        // slugs/IDs
            $table->enum('status', ['abierta', 'seleccion', 'cerrada'])->default('abierta');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
