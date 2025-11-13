<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguagesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Evita duplicar si ya hay datos
        if (DB::table('languages')->count() > 0) {
            return;
        }

        $now = now();

        // Detectamos columnas existentes (para no romper con esquemas distintos)
        $cols       = Schema::getColumnListing('languages');
        $hasName    = in_array('name', $cols, true);
        $hasNombre  = in_array('nombre', $cols, true);
        $hasCode    = in_array('code', $cols, true);
        $hasCodigo  = in_array('codigo', $cols, true);
        $hasCreated = in_array('created_at', $cols, true);
        $hasUpdated = in_array('updated_at', $cols, true);

        $base = [
            ['label' => 'Español',   'code' => 'es'],
            ['label' => 'Inglés',    'code' => 'en'],
            ['label' => 'Francés',   'code' => 'fr'],
            ['label' => 'Alemán',    'code' => 'de'],
            ['label' => 'Italiano',  'code' => 'it'],
            ['label' => 'Portugués', 'code' => 'pt'],
        ];

        $rows = [];
        foreach ($base as $item) {
            $row = [];

            // Nombre del idioma: rellena ambas si existen (por si alguna es NOT NULL)
            if ($hasName)   { $row['name']   = $item['label']; }
            if ($hasNombre) { $row['nombre'] = $item['label']; }

            // Código ISO: rellena ambas si existen
            if ($hasCode)   { $row['code']   = $item['code']; }
            if ($hasCodigo) { $row['codigo'] = $item['code']; }

            if ($hasCreated) { $row['created_at'] = $now; }
            if ($hasUpdated) { $row['updated_at'] = $now; }

            // Seguridad: si no hay ninguna columna de nombre, no podemos insertar
            if (!$hasName && !$hasNombre) {
                throw new \RuntimeException("La tabla 'languages' no tiene ni 'name' ni 'nombre'. Añade una de ellas.");
            }

            $rows[] = $row;
        }

        DB::table('languages')->insert($rows);
    }
}
