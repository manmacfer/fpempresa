<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // quién rechazó (empresa/admin)
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique('application_id'); // una sola entrada de rechazo por solicitud
        });
    }
    public function down(): void {
        Schema::dropIfExists('rejections');
    }
};
