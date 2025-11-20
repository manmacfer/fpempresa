<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Estructura completa consolidada de FPEmpresa
     */
    public function up(): void
    {
        // ========================================
        // SISTEMA: Laravel Core Tables
        // ========================================
        
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // ========================================
        // AUTENTICACIÓN: Users & Roles
        // ========================================
        
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role', 20)->nullable(); // LEGACY
            $table->foreignId('role_id')->nullable()->constrained('roles')->cascadeOnUpdate()->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ========================================
        // EDUCACIÓN: Teachers & Classrooms
        // ========================================
        
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('full_name');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('classroom_number', 50)->unique();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->cascadeOnDelete();
            $table->timestamps();
        });

        // ========================================
        // ALUMNOS: Students & Related
        // ========================================
        
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->boolean('validated')->default(false);

            // Académicos
            $table->string('cycle', 20)->nullable();
            $table->string('center', 190)->nullable();
            $table->integer('year_start')->nullable();
            $table->integer('year_end')->nullable();
            $table->string('fp_modality', 20)->nullable();

            // Personales / Contacto
            $table->string('dni', 32)->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 12)->nullable();
            $table->string('city', 190)->nullable();
            $table->boolean('has_driver_license')->nullable();
            $table->boolean('has_vehicle')->nullable();

            // Disponibilidad
            $table->string('availability_slot', 20)->nullable();
            $table->json('commitments')->nullable();
            $table->boolean('relocate')->nullable();
            $table->json('relocate_cities')->nullable();
            $table->boolean('transport_own')->nullable();
            $table->string('work_modality', 20)->nullable();
            $table->json('work_modalities')->nullable();
            $table->integer('remote_days')->nullable();
            $table->integer('days_per_week')->nullable();
            $table->date('available_from')->nullable();

            // Intereses / Perfil
            $table->json('sectors')->nullable();
            $table->string('preferred_company_type', 190)->nullable();
            $table->text('non_formal_experience')->nullable();
            $table->json('tech_competencies')->nullable();
            $table->json('soft_skills')->nullable();
            $table->json('languages')->nullable();
            $table->json('certifications')->nullable();

            // Extra
            $table->text('hobbies')->nullable();
            $table->text('clubs')->nullable();
            $table->boolean('align_activities')->nullable();
            $table->text('entrepreneurship')->nullable();
            $table->text('motivation')->nullable();
            $table->text('limitations')->nullable();

            // Enlaces
            $table->string('link_linkedin')->nullable();
            $table->string('link_portfolio')->nullable();
            $table->string('link_github')->nullable();
            $table->string('link_video')->nullable();

            // Archivos
            $table->string('cv_path')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->json('other_cert_paths')->nullable();
            $table->string('avatar_path')->nullable();

            $table->timestamps();
        });

        Schema::create('student_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('center')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('student_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('company', 190);
            $table->string('position', 190)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('functions')->nullable();
            $table->boolean('is_non_formal')->default(false);
            $table->timestamps();
        });

        // ========================================
        // EMPRESAS: Companies
        // ========================================
        
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('cif')->nullable();
            $table->string('sector')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // ========================================
        // OFERTAS: Vacancies & Applications
        // ========================================
        
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();

            // Requisitos
            $table->string('cycle_required', 50)->nullable();
            $table->boolean('accepts_fp_general')->default(true);
            $table->boolean('accepts_fp_dual')->default(true);
            $table->string('mode', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->unsignedTinyInteger('hours_per_week')->nullable();
            $table->unsignedSmallInteger('duration_weeks')->nullable();

            // Remuneración
            $table->boolean('paid')->default(false);
            $table->unsignedInteger('salary_month')->nullable();

            // Skills
            $table->json('tech_stack')->nullable();
            $table->json('soft_skills')->nullable();

            // Estado
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['cycle_required', 'mode']);
            $table->index(['city', 'province']);
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('state', ['enviada', 'vista', 'entrevista', 'rechazada', 'aceptada'])->default('enviada');
            $table->json('feedback')->nullable();
            $table->timestamps();

            $table->unique(['vacancy_id', 'student_id']);
        });

        // ========================================
        // MATCHING: Sistema de emparejamiento
        // ========================================
        
        Schema::create('matchings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['student_id', 'vacancy_id'], 'matchings_student_vacancy_unique');
            $table->index('status');
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('status', ['ACTIVO', 'CERRADO'])->default('ACTIVO');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->unique(['vacancy_id', 'student_id']);
            $table->index(['vacancy_id', 'status']);
            $table->index(['student_id', 'status']);
        });

        Schema::create('rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['vacancy_id', 'student_id']);
        });

        // ========================================
        // COMUNICACIÓN: Conversations & Messages
        // ========================================
        
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matching_id')->constrained('matchings')->cascadeOnDelete();
            $table->timestamps();

            $table->index('matching_id');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index('user_id');
        });

        Schema::create('matching_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matching_id')->constrained('matchings')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->string('file_path');
            $table->timestamps();

            $table->index('matching_id');
        });

        // ========================================
        // NOTIFICACIONES
        // ========================================
        
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });

        // ========================================
        // COMPETENCIAS Y IDIOMAS
        // ========================================
        
        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        // Tablas pivot
        Schema::create('alumno_competencia', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->primary(['student_id', 'competency_id']);
        });

        Schema::create('vacante_competencia_req', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->boolean('es_obligatoria')->default(true);
            $table->primary(['vacancy_id', 'competency_id']);
        });

        Schema::create('alumno_idioma', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->enum('nivel', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->default('A2');
            $table->primary(['student_id', 'language_id']);
        });

        Schema::create('vacante_idioma_req', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->enum('nivel_minimo', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->default('A2');
            $table->primary(['vacancy_id', 'language_id']);
        });

        // ========================================
        // GEOGRAFÍA: Municipalities & Provinces
        // ========================================
        
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('name', 150);
            $table->string('province_code', 2);
            $table->string('province_name', 100);
            $table->timestamps();

            $table->index('province_code');
            $table->index('province_name');
            $table->index(['name', 'province_name']);
        });

        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar en orden inverso respetando foreign keys
        Schema::dropIfExists('vacante_idioma_req');
        Schema::dropIfExists('alumno_idioma');
        Schema::dropIfExists('vacante_competencia_req');
        Schema::dropIfExists('alumno_competencia');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('competencies');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('matching_documents');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('rejections');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('matchings');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('student_experiences');
        Schema::dropIfExists('student_educations');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
