<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Usuarios COMPANY: crear companies si faltan y borrar students asociados
        $companyUsers = DB::table('users')->where('role', 'company')->pluck('id')->all();

        if ($companyUsers) {
            // Crea companies faltantes con trade_name/name igual a users.name
            $missingCompanies = DB::select("
                SELECT u.id as user_id, u.name
                FROM users u
                LEFT JOIN companies c ON c.user_id = u.id
                WHERE u.role = 'company' AND c.user_id IS NULL
            ");

            foreach ($missingCompanies as $row) {
                DB::table('companies')->insert([
                    'user_id'    => $row->user_id,
                    'name'       => $row->name,      // compat con esquemas viejos
                    'trade_name' => $row->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Borra students “colados” de users company
            DB::table('students')->whereIn('user_id', $companyUsers)->delete();
        }

        // 2) Usuarios STUDENT: crear students mínimos si faltan
        $studentUsers = DB::select("
            SELECT u.id as user_id, u.name
            FROM users u
            LEFT JOIN students s ON s.user_id = u.id
            WHERE u.role = 'student' AND s.user_id IS NULL
        ");
        foreach ($studentUsers as $row) {
            $parts = preg_split('/\s+/', trim($row->name ?? ''), 2);
            DB::table('students')->insert([
                'user_id'    => $row->user_id,
                'first_name' => $parts[0] ?? ($row->name ?? 'Alumno'),
                'last_name'  => $parts[1] ?? null,
                'cycle'      => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // No revertimos limpieza
    }
};
