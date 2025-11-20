<?php

namespace Database\Seeders;

use App\Models\Municipality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cargar datos de provincias
        $provinciasJson = file_get_contents(database_path('seeders/provincias.json'));
        $provincias = json_decode($provinciasJson, true);
        
        // Crear mapa de código a nombre de provincia
        $provinciaMap = [];
        foreach ($provincias as $provincia) {
            $provinciaMap[$provincia['id']] = $provincia['nm'];
        }
        
        // Cargar datos de municipios
        $municipiosJson = file_get_contents(database_path('seeders/municipios.json'));
        $municipios = json_decode($municipiosJson, true);
        
        echo "Importando " . count($municipios) . " municipios...\n";
        
        // Preparar datos para inserción por lotes
        $data = [];
        $batchSize = 500;
        
        foreach ($municipios as $index => $municipio) {
            $provinceCode = substr($municipio['id'], 0, 2);
            $provinceName = $provinciaMap[$provinceCode] ?? 'Desconocida';
            
            $data[] = [
                'code' => $municipio['id'],
                'name' => $municipio['nm'],
                'province_code' => $provinceCode,
                'province_name' => $provinceName,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Insertar por lotes para mejor rendimiento
            if (count($data) >= $batchSize) {
                DB::table('municipalities')->insert($data);
                echo "Insertados " . ($index + 1) . " municipios...\n";
                $data = [];
            }
        }
        
        // Insertar el último lote
        if (!empty($data)) {
            DB::table('municipalities')->insert($data);
        }
        
        echo "✓ Importación completada: " . count($municipios) . " municipios\n";
    }
}
