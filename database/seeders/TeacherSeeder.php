<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Role;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el rol de teacher
        $teacherRole = Role::where('slug', 'teacher')->first();
        
        if (!$teacherRole) {
            $this->command->error('El rol "teacher" no existe en la base de datos.');
            return;
        }

        // Crear usuario profesor
        $user = User::create([
            'name' => 'Profesor Demo',
            'email' => 'profesor@email.com',
            'password' => Hash::make('fpprofesor2025'),
            'role_id' => $teacherRole->id,
            'email_verified_at' => now(),
        ]);

        // Crear registro de profesor
        Teacher::create([
            'user_id' => $user->id,
            'full_name' => 'Profesor Demo',
        ]);

        $this->command->info('Usuario profesor creado exitosamente:');
        $this->command->info('Email: profesor@email.com');
        $this->command->info('Contraseña: fpprofesor2025');
    }
}
