<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matching;

class MatchingTestSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un matching de prueba completo (alumno y empresa han confirmado)
        // pero pendiente de validación por el centro
        Matching::create([
            'student_id' => 7,      // Alumno4
            'company_id' => 5,      // Libreria
            'status' => 'pending',
            'student_matched' => true,
            'company_matched' => true,
            'validado_centro' => false,
        ]);

        echo "✓ Matching de prueba creado correctamente\n";
    }
}
