<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CompanyController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil de usuario (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ÁREA PRIVADA por rol
Route::middleware(['auth','verified'])->group(function () {
    // Estudiantes (asumiendo que ya existen estos métodos en tu StudentController)
    Route::get('/students/me/edit', [StudentController::class,'editMe'])->name('students.edit.me');
    Route::put('/students/me',      [StudentController::class,'updateMe'])->name('students.update.me');

    // Empresas
    Route::get('/companies/me/edit', [CompanyController::class,'editMe'])->name('companies.edit.me');
    Route::put('/companies/me',      [CompanyController::class,'updateMe'])->name('companies.update.me');
});

// PÚBLICAS
Route::get('/students/{student}',  [StudentController::class,'publicShow'])->name('students.public.show');
Route::get('/companies/{company}', [CompanyController::class,'publicShow'])->name('companies.public.show');

require __DIR__.'/auth.php';
