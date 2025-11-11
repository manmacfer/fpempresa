<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta todos los seeders de la base de datos.
     */
    public function run(): void
    {
        $this->call([
            CatalogosSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
