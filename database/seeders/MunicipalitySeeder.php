<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Datos de municipios españoles basados en códigos INE
     * Para una lista completa, se puede importar desde:
     * https://www.ine.es/daco/daco42/codmun/cod_municipios_provincia.htm
     */
    public function run(): void
    {
        // Ejemplo de municipios principales de algunas provincias
        // En producción, deberías cargar el archivo completo del INE
        
        $municipalities = [
            // Madrid (28)
            ['code' => '28001', 'name' => 'Ajalvir', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28079', 'name' => 'Madrid', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28006', 'name' => 'Alcalá de Henares', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28007', 'name' => 'Alcobendas', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28013', 'name' => 'Alcorcón', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28074', 'name' => 'Leganés', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28065', 'name' => 'Getafe', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28080', 'name' => 'Móstoles', 'province_code' => '28', 'province_name' => 'Madrid'],
            ['code' => '28148', 'name' => 'Torrejón de Ardoz', 'province_code' => '28', 'province_name' => 'Madrid'],
            
            // Barcelona (08)
            ['code' => '08019', 'name' => 'Barcelona', 'province_code' => '08', 'province_name' => 'Barcelona'],
            ['code' => '08015', 'name' => 'Badalona', 'province_code' => '08', 'province_name' => 'Barcelona'],
            ['code' => '08187', 'name' => 'Sabadell', 'province_code' => '08', 'province_name' => 'Barcelona'],
            ['code' => '08221', 'name' => 'Terrassa', 'province_code' => '08', 'province_name' => 'Barcelona'],
            ['code' => '08101', 'name' => 'Hospitalet de Llobregat', 'province_code' => '08', 'province_name' => 'Barcelona'],
            ['code' => '08096', 'name' => 'Granollers', 'province_code' => '08', 'province_name' => 'Barcelona'],
            ['code' => '08121', 'name' => 'Mataró', 'province_code' => '08', 'province_name' => 'Barcelona'],
            
            // Valencia (46)
            ['code' => '46250', 'name' => 'Valencia', 'province_code' => '46', 'province_name' => 'Valencia'],
            ['code' => '46102', 'name' => 'Gandia', 'province_code' => '46', 'province_name' => 'Valencia'],
            ['code' => '46131', 'name' => 'Manises', 'province_code' => '46', 'province_name' => 'Valencia'],
            ['code' => '46171', 'name' => 'Paterna', 'province_code' => '46', 'province_name' => 'Valencia'],
            ['code' => '46184', 'name' => 'Quart de Poblet', 'province_code' => '46', 'province_name' => 'Valencia'],
            ['code' => '46244', 'name' => 'Torrent', 'province_code' => '46', 'province_name' => 'Valencia'],
            
            // Sevilla (41)
            ['code' => '41091', 'name' => 'Sevilla', 'province_code' => '41', 'province_name' => 'Sevilla'],
            ['code' => '41004', 'name' => 'Alcalá de Guadaíra', 'province_code' => '41', 'province_name' => 'Sevilla'],
            ['code' => '41020', 'name' => 'Dos Hermanas', 'province_code' => '41', 'province_name' => 'Sevilla'],
            ['code' => '41038', 'name' => 'Écija', 'province_code' => '41', 'province_name' => 'Sevilla'],
            ['code' => '41083', 'name' => 'San Juan de Aznalfarache', 'province_code' => '41', 'province_name' => 'Sevilla'],
            
            // Málaga (29)
            ['code' => '29067', 'name' => 'Málaga', 'province_code' => '29', 'province_name' => 'Málaga'],
            ['code' => '29054', 'name' => 'Marbella', 'province_code' => '29', 'province_name' => 'Málaga'],
            ['code' => '29015', 'name' => 'Benalmádena', 'province_code' => '29', 'province_name' => 'Málaga'],
            ['code' => '29051', 'name' => 'Fuengirola', 'province_code' => '29', 'province_name' => 'Málaga'],
            ['code' => '29094', 'name' => 'Vélez-Málaga', 'province_code' => '29', 'province_name' => 'Málaga'],
            
            // Zaragoza (50)
            ['code' => '50297', 'name' => 'Zaragoza', 'province_code' => '50', 'province_name' => 'Zaragoza'],
            ['code' => '50083', 'name' => 'Calatayud', 'province_code' => '50', 'province_name' => 'Zaragoza'],
            ['code' => '50251', 'name' => 'Tarazona', 'province_code' => '50', 'province_name' => 'Zaragoza'],
            
            // Alicante (03)
            ['code' => '03014', 'name' => 'Alicante', 'province_code' => '03', 'province_name' => 'Alicante'],
            ['code' => '03065', 'name' => 'Elche', 'province_code' => '03', 'province_name' => 'Alicante'],
            ['code' => '03031', 'name' => 'Benidorm', 'province_code' => '03', 'province_name' => 'Alicante'],
            ['code' => '03114', 'name' => 'Orihuela', 'province_code' => '03', 'province_name' => 'Alicante'],
            ['code' => '03140', 'name' => 'Torrevieja', 'province_code' => '03', 'province_name' => 'Alicante'],
            
            // Vizcaya (48)
            ['code' => '48020', 'name' => 'Bilbao', 'province_code' => '48', 'province_name' => 'Vizcaya'],
            ['code' => '48015', 'name' => 'Barakaldo', 'province_code' => '48', 'province_name' => 'Vizcaya'],
            ['code' => '48078', 'name' => 'Getxo', 'province_code' => '48', 'province_name' => 'Vizcaya'],
            ['code' => '48082', 'name' => 'Portugalete', 'province_code' => '48', 'province_name' => 'Vizcaya'],
            
            // A Coruña (15)
            ['code' => '15030', 'name' => 'A Coruña', 'province_code' => '15', 'province_name' => 'A Coruña'],
            ['code' => '15036', 'name' => 'Ferrol', 'province_code' => '15', 'province_name' => 'A Coruña'],
            ['code' => '15078', 'name' => 'Santiago de Compostela', 'province_code' => '15', 'province_name' => 'A Coruña'],
        ];
        
        foreach ($municipalities as $municipality) {
            DB::table('municipalities')->updateOrInsert(
                ['code' => $municipality['code']],
                array_merge($municipality, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
        
        $this->command->info('Seeded ' . count($municipalities) . ' municipalities.');
        $this->command->warn('NOTA: Esto es solo una muestra. Para producción, importa el listado completo del INE.');
        $this->command->info('Descarga: https://www.ine.es/daco/daco42/codmun/cod_municipios_provincia.htm');
    }
}
