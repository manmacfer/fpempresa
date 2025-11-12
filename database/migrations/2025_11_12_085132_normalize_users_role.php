<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Pásala a VARCHAR(20) NULL SIN default (ajusta si usas MariaDB < 10.3)
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NULL");
        // Limpia valores vacíos a NULL (por si existían)
        DB::statement("UPDATE users SET role = NULL WHERE role = ''");
    }

    public function down(): void
    {
        // Reversión simple; mantenemos VARCHAR(20) NULL
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NULL");
    }
};
