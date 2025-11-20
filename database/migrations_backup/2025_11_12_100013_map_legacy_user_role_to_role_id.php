<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Asegura que existen roles base (por si el seeder no corrió aún)
        $ensure = [
            1 => ['slug'=>'admin',   'name'=>'Administrador'],
            2 => ['slug'=>'teacher', 'name'=>'Profesor'],
            3 => ['slug'=>'student', 'name'=>'Alumno'],
            4 => ['slug'=>'company', 'name'=>'Empresa'],
        ];
        foreach ($ensure as $id => $r) {
            DB::table('roles')->updateOrInsert(['id'=>$id], array_merge($r, ['created_at'=>now(),'updated_at'=>now()]));
        }

        // Mapeo de sinónimos en tu BDD “sucia”
        $map = [
            1 => ['admin','administrator','administrador','administradora'],
            2 => ['teacher','profesor','profesora','profes','docente','teachers'],
            3 => ['student','students','alumno','alumna','alumnos','alumnas','estudiante','estudiantes'],
            4 => ['company','companies','empresa','empresas','compania','compañia','compañía'],
        ];

        // Normaliza a minúsculas y sin espacios
        $normalizer = fn($s) => trim(mb_strtolower((string)$s));

        $users = DB::table('users')->select('id','role')->get();
        foreach ($users as $u) {
            $val = $normalizer($u->role);

            $roleId = null;
            foreach ($map as $id => $list) {
                if (in_array($val, array_map($normalizer, $list), true)) {
                    $roleId = $id;
                    break;
                }
            }

            // Si quedó null, no tocamos (podrás decidir luego qué hacer)
            if ($roleId) {
                DB::table('users')->where('id', $u->id)->update(['role_id' => $roleId]);
            }
        }
    }

    public function down(): void
    {
        // No revertimos el mapeo
    }
};
