<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VacancyController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentEducationController;
use App\Http\Controllers\StudentExperienceController;
use App\Http\Controllers\CompanyController;

Route::get('/', fn () => redirect()->route('dashboard'));

// Área privada (requiere login + email verificado)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [VacancyController::class, 'dashboard'])->name('dashboard');

    // Vacantes + aplicaciones
    Route::get('/vacantes', [VacancyController::class, 'index'])->name('vacancies.index');
    Route::get('/vacantes/crear', [VacancyController::class, 'create'])->name('vacancies.create');
    Route::post('/vacantes', [VacancyController::class, 'store'])->name('vacancies.store');
    Route::post('/vacantes/{vacancy}/aplicar', [ApplicationController::class, 'store'])->name('applications.store');

    // Matchings (API + atajos de estado)
    Route::apiResource('matchings', MatchingController::class);
    Route::patch('matchings/{matching}/accept', [MatchingController::class, 'accept'])->name('matchings.accept');
    Route::patch('matchings/{matching}/reject', [MatchingController::class, 'reject'])->name('matchings.reject');

    // --- PERFIL ALUMNO (sin ID) ---
    Route::middleware(['role:student'])->group(function () {
        // GET cómodo: /students/me -> /students/me/edit
        Route::get('/students/me', fn () => redirect()->route('students.edit.me'))->name('students.me.redirect');

        Route::get('/students/me/edit', [StudentController::class, 'editMe'])->name('students.edit.me');
        Route::match(['post', 'put', 'patch'], '/students/me', [StudentController::class, 'updateMe'])->name('students.update.me');

        // Formación (historial) del alumno autenticado
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
        // GET cómodo: /companies/me -> /companies/me/edit
        Route::get('/companies/me', fn () => redirect()->route('companies.edit.me'))->name('companies.me.redirect');

        Route::get('/companies/me/edit', [CompanyController::class, 'editMe'])->name('companies.edit.me');
        Route::match(['post', 'put', 'patch'], '/companies/me', [CompanyController::class, 'updateMe'])->name('companies.update.me');
    });
});

// PÚBLICAS (perfiles visibles sin login)
Route::get('/students/public/{student}', [StudentController::class, 'publicShow'])->name('students.public.show');
Route::get('/companies/public/{company}', [CompanyController::class, 'publicShow'])->name('companies.public.show');

require __DIR__ . '/auth.php';
