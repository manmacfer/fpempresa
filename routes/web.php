<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VacancyStudentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentEducationController;
use App\Http\Controllers\StudentExperienceController;
use App\Http\Controllers\CompanyController;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [VacancyController::class, 'dashboard'])->name('dashboard');

    // ---- VACANTES (GENERAL) ----
    // 1) Índice general
    //
    // AHORA:
    // - Para alumnos: VacancyStudentController@index -> usa CompatibilityService y devuelve vacantes ordenadas por score (>=50%)
    // - Para el resto de roles, dentro del controller puedes delegar a VacancyController@index si quieres.
    Route::get('/vacantes', [VacancyStudentController::class, 'index'])->name('vacancies.index');

    // 2) Rutas SOLO empresa (colocadas ANTES de /vacantes/{vacancy})
    Route::middleware(['role:company'])->group(function () {
        Route::get('/vacantes/crear', [VacancyController::class, 'create'])->name('vacancies.create');
        Route::post('/vacantes',      [VacancyController::class, 'store'])->name('vacancies.store');
        Route::get('/vacantes/mis',   [VacancyController::class, 'myIndex'])->name('vacancies.my');
    });

    // 3) Ver una vacante (después de las estáticas y limitado a numérico)
    Route::get('/vacantes/{vacancy}', [VacancyController::class, 'show'])
        ->whereNumber('vacancy')
        ->name('vacancies.show');

    // Aplicaciones (alumno aplica a una vacante)
    Route::post('/vacantes/{vacancy}/aplicar', [ApplicationController::class, 'store'])
        ->whereNumber('vacancy')
        ->name('applications.store');

    // Matchings (API + atajos de estado)
    Route::apiResource('matchings', MatchingController::class);
    Route::patch('matchings/{matching}/accept', [MatchingController::class, 'accept'])->name('matchings.accept');
    Route::patch('matchings/{matching}/reject', [MatchingController::class, 'reject'])->name('matchings.reject');

    // --- PERFIL ALUMNO (sin ID) ---
    Route::middleware(['role:student'])->group(function () {
        Route::get('/students/me', fn () => redirect()->route('students.edit.me'))->name('students.me.redirect');
        Route::get('/students/me/edit', [StudentController::class, 'editMe'])->name('students.edit.me');
        Route::match(['post', 'put', 'patch'], '/students/me', [StudentController::class, 'updateMe'])->name('students.update.me');

        // Formación del alumno autenticado
        Route::post('/students/me/educations', [StudentEducationController::class, 'store'])->name('students.educations.store');
        Route::patch('/students/me/educations/{education}', [StudentEducationController::class, 'update'])->name('students.educations.update');
        Route::delete('/students/me/educations/{education}', [StudentEducationController::class, 'destroy'])->name('students.educations.destroy');

        // Experiencia del alumno autenticado
        Route::post('/students/me/experiences', [StudentExperienceController::class, 'store'])->name('students.experiences.store');
        Route::patch('/students/me/experiences/{experience}', [StudentExperienceController::class, 'update'])->name('students.experiences.update');
        Route::delete('/students/me/experiences/{experience}', [StudentExperienceController::class, 'destroy'])->name('students.experiences.destroy');
    });

    // --- PERFIL EMPRESA (sin ID) ---
    Route::middleware(['role:company'])->group(function () {
        Route::get('/companies/me', fn () => redirect()->route('companies.edit.me'))->name('companies.me.redirect');
        Route::get('/companies/me/edit', [CompanyController::class, 'editMe'])->name('companies.edit.me');
        Route::match(['post', 'put', 'patch'], '/companies/me', [CompanyController::class, 'updateMe'])->name('companies.update.me');
    });
});

// PÚBLICAS (perfiles)
Route::get('/students/public/{student}', [StudentController::class, 'publicShow'])->name('students.public.show');
Route::get('/companies/public/{company}', [CompanyController::class, 'publicShow'])->name('companies.public.show');

require __DIR__ . '/auth.php';
