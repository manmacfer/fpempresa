<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        // IDs fijos como pediste
        $rows = [
            ['id' => 1, 'slug' => 'admin',   'name' => 'Administrador', 'created_at'=>now(),'updated_at'=>now()],
            ['id' => 2, 'slug' => 'teacher', 'name' => 'Profesor',      'created_at'=>now(),'updated_at'=>now()],
            ['id' => 3, 'slug' => 'student', 'name' => 'Alumno',        'created_at'=>now(),'updated_at'=>now()],
            ['id' => 4, 'slug' => 'company', 'name' => 'Empresa',       'created_at'=>now(),'updated_at'=>now()],
        ];

        foreach ($rows as $r) {
            DB::table('roles')->updateOrInsert(['id' => $r['id']], $r);
        }
    }
}
