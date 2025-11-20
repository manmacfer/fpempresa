<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NULL");
        DB::statement("UPDATE users SET role = NULL WHERE role = '' ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NULL");
    }
};
