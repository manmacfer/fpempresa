<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Vacancy;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Profesor + Aula
        $uProf = User::updateOrCreate(['email'=>'profe@velazquez.test'], [
            'name'=>'Profe Velazquez', 'password'=>Hash::make('password'), 'role'=>'TUTOR'
        ]);
        $profe = Teacher::updateOrCreate(['user_id'=>$uProf->id], ['full_name'=>'Profe Velazquez']);
        $aula  = Classroom::updateOrCreate(['nombre'=>'2º DAW A', 'teacher_id'=>$profe->id]);

        // Empresa + Company
        $uEmp = User::updateOrCreate(['email'=>'empresa@velazquez.test'], [
            'name'=>'Empresa Demo', 'password'=>Hash::make('password'), 'role'=>'EMPRESA',
        ]);
        $company = Company::updateOrCreate(['user_id'=>$uEmp->id], ['name'=>'Empresa Demo S.L.','ubicacion'=>'Sevilla']);

        // Vacantes nuevas (con campos buenos)
        Vacancy::updateOrCreate(['company_id'=>$company->id, 'title'=>'Backend Laravel'], [
            'description'=>'Soporte y desarrollo Laravel',
            'ciclo_formativo_req'=>'DAW',
            'modalidad'=>'HIBRIDA',
            'ubicacion'=>'Sevilla',
            'horarios_disponibles'=>json_encode(['mañana','tarde']),
            'requiere_carnet'=>false,
            'permite_teletrabajo'=>true,
            'estado_vacante'=>'ABIERTA',
        ]);

        Vacancy::updateOrCreate(['company_id'=>$company->id, 'title'=>'Frontend Vue'], [
            'description'=>'Maquetación con Vue',
            'ciclo_formativo_req'=>'DAW',
            'modalidad'=>'REMOTA',
            'ubicacion'=>'Cádiz',
            'horarios_disponibles'=>json_encode(['tarde']),
            'requiere_carnet'=>false,
            'permite_teletrabajo'=>true,
            'estado_vacante'=>'ABIERTA',
        ]);

        // Alumnos (enlazados a aula)
        $uAl1 = User::updateOrCreate(['email'=>'alumno1@velazquez.test'], [
            'name'=>'Alumno Uno','password'=>Hash::make('password'),'role'=>'ALUMNO'
        ]);
        Student::updateOrCreate(['user_id'=>$uAl1->id], [
            'full_name'=>'Alumno Uno','ciclo'=>'DAW','ubicacion'=>'Sevilla','classroom_id'=>$aula->id
        ]);

        $uAl2 = User::updateOrCreate(['email'=>'alumno2@velazquez.test'], [
            'name'=>'Alumno Dos','password'=>Hash::make('password'),'role'=>'ALUMNO'
        ]);
        Student::updateOrCreate(['user_id'=>$uAl2->id], [
            'full_name'=>'Alumno Dos','ciclo'=>'DAM','ubicacion'=>'Sevilla','classroom_id'=>$aula->id
        ]);
    }
}
