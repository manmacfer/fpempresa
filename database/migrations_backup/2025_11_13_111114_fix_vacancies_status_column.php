<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Abre la columna a VARCHAR temporalmente para poder mapear valores
        DB::statement("ALTER TABLE vacancies MODIFY status VARCHAR(20) NULL");

        // 2) Normaliza posibles valores antiguos
        // numéricos -> strings
        DB::statement("UPDATE vacancies SET status = 'draft'     WHERE status IS NULL OR status = '' OR status = '0'");
        DB::statement("UPDATE vacancies SET status = 'published' WHERE status IN ('1','publicada','publicado','publish')");
        DB::statement("UPDATE vacancies SET status = 'closed'    WHERE status IN ('2','cerrada','cerrado','close','closed')");

        // 3) Cierra como ENUM válido
        DB::statement("ALTER TABLE vacancies MODIFY status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Si quieres, puedes dejarla en VARCHAR(20) de nuevo
        DB::statement("ALTER TABLE vacancies MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
    }
};
