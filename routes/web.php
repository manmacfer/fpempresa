<?php

use App\Http\Controllers\VacancyController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\StudentController;

Route::get('/', fn() => redirect('/dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [VacancyController::class, 'dashboard'])->name('dashboard');

    Route::get('/vacantes', [VacancyController::class, 'index'])->name('vacancies.index');
    Route::get('/vacantes/crear', [VacancyController::class, 'create'])->name('vacancies.create');
    Route::post('/vacantes', [VacancyController::class, 'store'])->name('vacancies.store');
    Route::post('/vacantes/{vacancy}/aplicar', [ApplicationController::class, 'store'])->name('applications.store');
    Route::apiResource('matchings', \App\Http\Controllers\MatchingController::class);
    Route::get('/perfil/alumno', [StudentController::class, 'edit'])->name('students.edit');
    Route::patch('/perfil/alumno', [StudentController::class, 'update'])->name('students.update');

    // Atajos de estado:
    Route::patch('matchings/{matching}/accept', [MatchingController::class, 'accept'])
        ->name('matchings.accept');

    Route::patch('matchings/{matching}/reject', [MatchingController::class, 'reject'])
        ->name('matchings.reject');

    // Página PÚBLICA del alumno (no requiere login)
    Route::get('/alumnos/{student}', [StudentController::class, 'publicShow'])
        ->name('students.public.show');
});

require __DIR__ . '/auth.php';
