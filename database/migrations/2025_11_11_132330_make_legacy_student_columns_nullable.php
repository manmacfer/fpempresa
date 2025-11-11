<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students','full_name')) {
                $table->string('full_name', 255)->nullable()->change();
            }
            if (Schema::hasColumn('students','ciclo')) {
                $table->string('ciclo', 190)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students','full_name')) {
                $table->string('full_name', 255)->nullable(false)->change();
            }
            if (Schema::hasColumn('students','ciclo')) {
                $table->string('ciclo', 190)->nullable(false)->change();
            }
        });
    }
};
