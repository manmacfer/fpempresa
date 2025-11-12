<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Rol como VARCHAR(20) sin default raro
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NULL");

        // Limpia vacíos a NULL
        DB::statement("UPDATE users SET role = NULL WHERE role = '' ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NULL");
    }
};
