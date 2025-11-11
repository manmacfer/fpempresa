<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VacancyController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentEducationController;
use App\Http\Controllers\StudentExperienceController;

Route::get('/', fn () => redirect()->route('dashboard'));

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
    Route::get('/students/me/edit', [StudentController::class, 'editMe'])->name('students.edit.me');
    Route::match(['post','patch'], '/students/me', [StudentController::class, 'updateMe'])->name('students.update.me');

    // Perfil público
    Route::get('/students/public/{student}', [StudentController::class, 'publicShow'])->name('students.public.show');

    // Formación (historial) del alumno autenticado
    Route::post('/students/me/educations', [StudentEducationController::class, 'store'])->name('students.educations.store');
    Route::patch('/students/me/educations/{education}', [StudentEducationController::class, 'update'])->name('students.educations.update');
    Route::delete('/students/me/educations/{education}', [StudentEducationController::class, 'destroy'])->name('students.educations.destroy');

    // Experiencia del alumno autenticado
    Route::post('/students/me/experiences', [StudentExperienceController::class, 'store'])->name('students.experiences.store');
    Route::patch('/students/me/experiences/{experience}', [StudentExperienceController::class, 'update'])->name('students.experiences.update');
    Route::delete('/students/me/experiences/{experience}', [StudentExperienceController::class, 'destroy'])->name('students.experiences.destroy');

    // ⚠️ NO definimos aquí students.edit / students.update del resource para evitar conflicto de nombres.
    // Si quieres mantener un show por ID para admins, podrías añadir:
    // Route::resource('students', StudentController::class)->only(['show']);
});

require __DIR__.'/auth.php';
