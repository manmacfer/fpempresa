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
        // Tabla de conversaciones (una por matching)
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matching_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('matching_id');
        });

        // Tabla de mensajes
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['conversation_id', 'created_at']);
            $table->index('user_id');
        });

        // Tabla para subir documentos del matching
        Schema::create('matching_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matching_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // convenio, informe, etc.
            $table->string('file_path');
            $table->timestamps();
            
            $table->index('matching_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('matching_documents');
    }
};
