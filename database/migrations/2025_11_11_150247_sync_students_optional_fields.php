<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::table('students', function (Blueprint $table) {
// Académicos básicos
if (!Schema::hasColumn('students','cycle')) {
$table->string('cycle', 20)->nullable()->after('user_id');
}
if (!Schema::hasColumn('students','center')) {
$table->string('center', 190)->nullable();
}
if (!Schema::hasColumn('students','year_start')) {
$table->integer('year_start')->nullable();
}
if (!Schema::hasColumn('students','year_end')) {
$table->integer('year_end')->nullable();
}
if (!Schema::hasColumn('students','fp_modality')) {
$table->string('fp_modality', 20)->nullable(); // dual|general
}


// Personales / contacto
if (!Schema::hasColumn('students','dni')) {
$table->string('dni', 32)->nullable();
}
if (!Schema::hasColumn('students','phone')) {
$table->string('phone', 30)->nullable();
}
if (!Schema::hasColumn('students','birth_date')) {
$table->date('birth_date')->nullable();
}
if (!Schema::hasColumn('students','address')) {
$table->string('address', 255)->nullable();
}
if (!Schema::hasColumn('students','postal_code')) {
$table->string('postal_code', 12)->nullable();
}
if (!Schema::hasColumn('students','city')) {
$table->string('city', 190)->nullable();
}
if (!Schema::hasColumn('students','has_driver_license')) {
$table->boolean('has_driver_license')->nullable();
}
if (!Schema::hasColumn('students','has_vehicle')) {
$table->boolean('has_vehicle')->nullable();
}


// Disponibilidad
if (!Schema::hasColumn('students','availability_slot')) {
$table->string('availability_slot', 20)->nullable(); // morning|afternoon|both
}
if (!Schema::hasColumn('students','commitments')) {
$table->json('commitments')->nullable();
}
if (!Schema::hasColumn('students','relocate')) {
$table->boolean('relocate')->nullable();
}
if (!Schema::hasColumn('students','relocate_cities')) {
$table->json('relocate_cities')->nullable();
}
if (!Schema::hasColumn('students','transport_own')) {
$table->boolean('transport_own')->nullable();
}
if (!Schema::hasColumn('students','work_modality')) {
$table->string('work_modality', 20)->nullable(); // presencial|remota|hibrida
}
if (!Schema::hasColumn('students','remote_days')) {
$table->integer('remote_days')->nullable();
}
if (!Schema::hasColumn('students','days_per_week')) {
$table->integer('days_per_week')->nullable();
}
if (!Schema::hasColumn('students','available_from')) {
$table->date('available_from')->nullable();
}


// Intereses / perfil profesional
if (!Schema::hasColumn('students','sectors')) {
$table->json('sectors')->nullable();
}
if (!Schema::hasColumn('students','preferred_company_type')) {
$table->string('preferred_company_type', 190)->nullable();
}
if (!Schema::hasColumn('students','non_formal_experience')) {
$table->text('non_formal_experience')->nullable();
}
if (!Schema::hasColumn('students','tech_competencies')) {
$table->json('tech_competencies')->nullable();
}
if (!Schema::hasColumn('students','soft_skills')) {
$table->json('soft_skills')->nullable();
}
if (!Schema::hasColumn('students','languages')) {
$table->json('languages')->nullable(); // [{name,level}]
}
if (!Schema::hasColumn('students','certifications')) {
$table->json('certifications')->nullable();
}


// Extra
if (!Schema::hasColumn('students','hobbies')) {
$table->text('hobbies')->nullable();
}
if (!Schema::hasColumn('students','clubs')) {
$table->text('clubs')->nullable();
}
if (!Schema::hasColumn('students','align_activities')) {
$table->boolean('align_activities')->nullable(); // vincular oportunidades con actividades
}
if (!Schema::hasColumn('students','entrepreneurship')) {
$table->text('entrepreneurship')->nullable(); // proyectos/iniciativas
}


// Enlaces
if (!Schema::hasColumn('students','link_linkedin')) {
$table->string('link_linkedin', 255)->nullable();
}
if (!Schema::hasColumn('students','link_portfolio')) {
$table->string('link_portfolio', 255)->nullable();
}
if (!Schema::hasColumn('students','link_github')) {
$table->string('link_github', 255)->nullable();
}
if (!Schema::hasColumn('students','link_video')) {
$table->string('link_video', 255)->nullable();
}


// Archivos
if (!Schema::hasColumn('students','cv_path')) {
$table->string('cv_path', 255)->nullable();
}
if (!Schema::hasColumn('students','cover_letter_path')) {
$table->string('cover_letter_path', 255)->nullable();
}
if (!Schema::hasColumn('students','other_cert_paths')) {
$table->json('other_cert_paths')->nullable();
}
if (!Schema::hasColumn('students','avatar_path')) {
$table->string('avatar_path', 255)->nullable();
}
});
}


public function down(): void
{
// No borramos columnas para no perder datos.
}
};