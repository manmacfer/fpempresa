<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'full_name')) {
                $table->dropColumn('full_name');
            }
        });
    }
    public function down(): void {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students','full_name')) {
                $table->string('full_name',255)->nullable();
            }
        });
    }
};
