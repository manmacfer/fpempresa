<?php

use App\Http\Controllers\VacancyController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchingController;

Route::get('/', fn() => redirect('/dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [VacancyController::class, 'dashboard'])->name('dashboard');

    Route::get('/vacantes', [VacancyController::class, 'index'])->name('vacancies.index');
    Route::get('/vacantes/crear', [VacancyController::class, 'create'])->name('vacancies.create');
    Route::post('/vacantes', [VacancyController::class, 'store'])->name('vacancies.store');
    Route::post('/vacantes/{vacancy}/aplicar', [ApplicationController::class, 'store'])->name('applications.store');
    Route::apiResource('matchings', \App\Http\Controllers\MatchingController::class);

    // Atajos de estado:
    Route::patch('matchings/{matching}/accept', [MatchingController::class, 'accept'])
        ->name('matchings.accept');

    Route::patch('matchings/{matching}/reject', [MatchingController::class, 'reject'])
        ->name('matchings.reject');
});

require __DIR__ . '/auth.php';
