<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Si NO existe, la creamos "limpia"
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('legal_name')->nullable();
                $table->string('trade_name')->nullable();
                $table->string('cif', 20)->nullable();
                $table->string('sector')->nullable();

                $table->string('website')->nullable();
                $table->string('linkedin')->nullable();

                $table->string('phone', 30)->nullable();
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->string('postal_code', 15)->nullable();

                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();

                $table->text('description')->nullable();

                $table->timestamps();
            });
            return;
        }

        // 2) Si SÍ existe, vemos si está vacía
        $count = 0;
        try {
            $count = DB::table('companies')->count();
        } catch (\Throwable $e) {
            // Si falla el count por esquema raro, la tratamos como vacía para regenerar
            $count = 0;
        }

        // 2a) Si está vacía, la recreamos con el esquema final (seguro, sin pérdida)
        if ($count === 0) {
            Schema::drop('companies');

            Schema::create('companies', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('legal_name')->nullable();
                $table->string('trade_name')->nullable();
                $table->string('cif', 20)->nullable();
                $table->string('sector')->nullable();

                $table->string('website')->nullable();
                $table->string('linkedin')->nullable();

                $table->string('phone', 30)->nullable();
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->string('postal_code', 15)->nullable();

                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();

                $table->text('description')->nullable();

                $table->timestamps();
            });
            return;
        }

        // 2b) Si tiene datos, añadimos SOLO columnas que falten (no destructivo)
        Schema::table('companies', function (Blueprint $table) {
            // Clave foránea a users + unique por empresa-usuario (si no existiera)
            if (!Schema::hasColumn('companies', 'user_id')) {
                $table->foreignId('user_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('companies', 'legal_name'))   $table->string('legal_name')->nullable();
            if (!Schema::hasColumn('companies', 'trade_name'))   $table->string('trade_name')->nullable();
            if (!Schema::hasColumn('companies', 'cif'))          $table->string('cif', 20)->nullable();
            if (!Schema::hasColumn('companies', 'sector'))       $table->string('sector')->nullable();
            if (!Schema::hasColumn('companies', 'website'))      $table->string('website')->nullable();
            if (!Schema::hasColumn('companies', 'linkedin'))     $table->string('linkedin')->nullable();
            if (!Schema::hasColumn('companies', 'phone'))        $table->string('phone', 30)->nullable();
            if (!Schema::hasColumn('companies', 'city'))         $table->string('city')->nullable();
            if (!Schema::hasColumn('companies', 'province'))     $table->string('province')->nullable();
            if (!Schema::hasColumn('companies', 'postal_code'))  $table->string('postal_code', 15)->nullable();
            if (!Schema::hasColumn('companies', 'contact_name')) $table->string('contact_name')->nullable();
            if (!Schema::hasColumn('companies', 'contact_email'))$table->string('contact_email')->nullable();
            if (!Schema::hasColumn('companies', 'description'))  $table->text('description')->nullable();

            // timestamps si faltaran
            if (!Schema::hasColumn('companies', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
