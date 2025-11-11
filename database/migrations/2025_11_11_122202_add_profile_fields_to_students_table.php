<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // PERSONALES / CONTACTO
            if (!Schema::hasColumn('students','phone'))              $table->string('phone', 30)->nullable();
            if (!Schema::hasColumn('students','dni'))                $table->string('dni', 32)->nullable();
            if (!Schema::hasColumn('students','birth_date'))         $table->date('birth_date')->nullable();
            if (!Schema::hasColumn('students','address'))            $table->string('address', 255)->nullable();
            if (!Schema::hasColumn('students','postal_code'))        $table->string('postal_code', 12)->nullable();
            if (!Schema::hasColumn('students','city'))               $table->string('city', 190)->nullable();
            if (!Schema::hasColumn('students','has_driver_license')) $table->boolean('has_driver_license')->nullable();
            if (!Schema::hasColumn('students','has_vehicle'))        $table->boolean('has_vehicle')->nullable();

            // ACADÉMICOS (ciclo actual)
            if (!Schema::hasColumn('students','cycle'))              $table->string('cycle', 190)->nullable();
            if (!Schema::hasColumn('students','center'))             $table->string('center', 190)->nullable();
            if (!Schema::hasColumn('students','year_start'))         $table->smallInteger('year_start')->unsigned()->nullable();
            if (!Schema::hasColumn('students','year_end'))           $table->smallInteger('year_end')->unsigned()->nullable();
            if (!Schema::hasColumn('students','fp_modality'))        $table->string('fp_modality', 20)->nullable(); // 'dual' | 'general'

            // DISPONIBILIDAD
            if (!Schema::hasColumn('students','availability_slot'))  $table->string('availability_slot', 20)->nullable(); // 'morning' | 'afternoon' | 'both'
            if (!Schema::hasColumn('students','commitments'))        $table->json('commitments')->nullable(); // p.ej. ["trabajo_parcial","cuidado_dependientes","otro: ..."]
            if (!Schema::hasColumn('students','relocate'))           $table->boolean('relocate')->nullable();
            if (!Schema::hasColumn('students','relocate_cities'))    $table->json('relocate_cities')->nullable();
            if (!Schema::hasColumn('students','transport_own'))      $table->boolean('transport_own')->nullable();
            if (!Schema::hasColumn('students','work_modality'))      $table->string('work_modality', 20)->nullable(); // 'presencial' | 'remota' | 'hibrida'
            if (!Schema::hasColumn('students','remote_days'))        $table->tinyInteger('remote_days')->unsigned()->nullable(); // si híbrida
            if (!Schema::hasColumn('students','days_per_week'))      $table->tinyInteger('days_per_week')->unsigned()->nullable();
            if (!Schema::hasColumn('students','available_from'))     $table->date('available_from')->nullable();

            // INTERESES / COMPETENCIAS / IDIOMAS
            if (!Schema::hasColumn('students','sectors'))            $table->json('sectors')->nullable();
            if (!Schema::hasColumn('students','preferred_company_type')) $table->string('preferred_company_type', 190)->nullable();
            if (!Schema::hasColumn('students','non_formal_experience'))  $table->text('non_formal_experience')->nullable();
            if (!Schema::hasColumn('students','tech_competencies'))  $table->json('tech_competencies')->nullable(); // ["Laravel","Vue",...]
            if (!Schema::hasColumn('students','soft_skills'))        $table->json('soft_skills')->nullable();       // ["Trabajo en equipo","Iniciativa",...]
            if (!Schema::hasColumn('students','languages'))          $table->json('languages')->nullable();         // [{"name":"Inglés","level":"B2"},...]
            if (!Schema::hasColumn('students','certifications'))     $table->json('certifications')->nullable();

            // EXTRA / LINKS
            if (!Schema::hasColumn('students','hobbies'))            $table->text('hobbies')->nullable();
            if (!Schema::hasColumn('students','clubs'))              $table->text('clubs')->nullable();
            if (!Schema::hasColumn('students','motivation'))         $table->text('motivation')->nullable();
            if (!Schema::hasColumn('students','limitations'))        $table->text('limitations')->nullable();

            if (!Schema::hasColumn('students','link_linkedin'))      $table->string('link_linkedin', 255)->nullable();
            if (!Schema::hasColumn('students','link_portfolio'))     $table->string('link_portfolio', 255)->nullable();
            if (!Schema::hasColumn('students','link_github'))        $table->string('link_github', 255)->nullable();
            if (!Schema::hasColumn('students','link_video'))         $table->string('link_video', 255)->nullable();

            // ARCHIVOS (para el futuro CV/CL)
            if (!Schema::hasColumn('students','cv_path'))            $table->string('cv_path', 255)->nullable();
            if (!Schema::hasColumn('students','cover_letter_path'))  $table->string('cover_letter_path', 255)->nullable();
            if (!Schema::hasColumn('students','other_cert_paths'))   $table->json('other_cert_paths')->nullable();

            // Avatar ya previsto
            if (!Schema::hasColumn('students','avatar_path'))        $table->string('avatar_path', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $cols = [
                'phone','dni','birth_date','address','postal_code','city','has_driver_license','has_vehicle',
                'cycle','center','year_start','year_end','fp_modality',
                'availability_slot','commitments','relocate','relocate_cities','transport_own','work_modality','remote_days','days_per_week','available_from',
                'sectors','preferred_company_type','non_formal_experience','tech_competencies','soft_skills','languages','certifications',
                'hobbies','clubs','motivation','limitations',
                'link_linkedin','link_portfolio','link_github','link_video',
                'cv_path','cover_letter_path','other_cert_paths',
                'avatar_path',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('students', $c)) $table->dropColumn($c);
            }
        });
    }
};
