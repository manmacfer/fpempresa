<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // Competencias (ejemplos)
        DB::table('competencies')->upsert([
            ['slug'=>'laravel','nombre'=>'Laravel'],
            ['slug'=>'vue','nombre'=>'Vue'],
            ['slug'=>'mysql','nombre'=>'MySQL'],
            ['slug'=>'git','nombre'=>'Git'],
            ['slug'=>'docker','nombre'=>'Docker'],
        ], ['slug'], ['nombre']);

        // Idiomas (ejemplos)
        DB::table('languages')->upsert([
            ['codigo'=>'ES','nombre'=>'Español'],
            ['codigo'=>'EN','nombre'=>'Inglés'],
            ['codigo'=>'FR','nombre'=>'Francés'],
        ], ['codigo'], ['nombre']);
    }
}
