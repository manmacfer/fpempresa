<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VacancyStudentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentEducationController;
use App\Http\Controllers\StudentExperienceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    // Ruta de espera para alumnos no validados (sin middleware validated.student)
    Route::get('/student/waiting', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        
        // Si el usuario ya está validado, redirigir al dashboard
        if ($user && $user->role === 'student') {
            $student = $user->student()->first();
            if ($student && $student->validated) {
                return redirect()->route('dashboard');
            }
        }
        
        return inertia('Students/Waiting');
    })->name('student.waiting');
});

Route::middleware(['auth', 'verified', 'validated.student'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [VacancyController::class, 'dashboard'])->name('dashboard');

    // Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Rutas SOLO empresa (antes de /vacantes/{vacancy})
    Route::middleware(['role:company'])->group(function () {
        Route::get('/vacantes/crear', [VacancyController::class, 'create'])->name('vacancies.create');
        Route::post('/vacantes', [VacancyController::class, 'store'])->name('vacancies.store');
        Route::get('/vacantes/mis', [VacancyController::class, 'myIndex'])->name('vacancies.my');

        // Perfil empresa sin ID
        Route::get('/companies/me', fn () => redirect()->route('companies.edit.me'))->name('companies.me.redirect');
        Route::get('/companies/me/edit', [CompanyController::class, 'editMe'])->name('companies.edit.me');
        Route::match(['post', 'put', 'patch'], '/companies/me', [CompanyController::class, 'updateMe'])->name('companies.update.me');
    });

    // Ver una vacante (limitado a numérico)
    Route::get('/vacantes/{vacancy}', [VacancyController::class, 'show'])
        ->whereNumber('vacancy')
        ->name('vacancies.show');

    // Aplicaciones (alumno aplica a una vacante)
    Route::post('/vacantes/{vacancy}/aplicar', [ApplicationController::class, 'store'])
        ->whereNumber('vacancy')
        ->name('applications.store');

    // Eliminar candidatura (alumno)
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])
        ->name('applications.destroy');

    // Matchings (API + atajos de estado)
    Route::apiResource('matchings', MatchingController::class);
    Route::patch('matchings/{matching}/accept', [MatchingController::class, 'accept'])->name('matchings.accept');
    Route::patch('matchings/{matching}/reject', [MatchingController::class, 'reject'])->name('matchings.reject');

    Route::get('/matching/company', [MatchingController::class, 'company'])->name('matching.company');
    Route::get('/matching/student', [MatchingController::class, 'student'])->name('matching.student');
    
    // Rechazar y aceptar candidaturas (empresa)
    Route::patch('/applications/{application}/reject', [MatchingController::class, 'reject'])->name('applications.reject');
    Route::patch('/applications/{application}/accept', [MatchingController::class, 'accept'])->name('applications.accept');

    // --- PERFIL ALUMNO (sin ID) + LISTADO DE VACANTES PARA ALUMNO ---
    Route::middleware(['role:student'])->group(function () {
        // Índice de vacantes con compatibilidad (solo alumnos)
        Route::get('/vacantes', [VacancyStudentController::class, 'index'])->name('vacancies.index');

        // Perfil alumno autenticado
        Route::get('/students/me', fn () => redirect()->route('students.edit.me'))->name('students.me.redirect');
        Route::get('/students/me/edit', [StudentController::class, 'editMe'])->name('students.edit.me');
        Route::match(['post', 'put', 'patch'], '/students/me', [StudentController::class, 'updateMe'])->name('students.update.me');

        // Perfil público del propio alumno (para Ziggy en Edit.vue)
        Route::get('/students/me/public', [StudentController::class, 'publicMe'])->name('students.public.me');

        // Formación del alumno autenticado
        Route::post('/students/me/educations', [StudentEducationController::class, 'store'])->name('students.educations.store');
        Route::patch('/students/me/educations/{education}', [StudentEducationController::class, 'update'])->name('students.educations.update');
        Route::delete('/students/me/educations/{education}', [StudentEducationController::class, 'destroy'])->name('students.educations.destroy');

        // Experiencia del alumno autenticado
        Route::post('/students/me/experiences', [StudentExperienceController::class, 'store'])->name('students.experiences.store');
        Route::patch('/students/me/experiences/{experience}', [StudentExperienceController::class, 'update'])->name('students.experiences.update');
        Route::delete('/students/me/experiences/{experience}', [StudentExperienceController::class, 'destroy'])->name('students.experiences.destroy');
    });

    // --- PANEL DEL PROFESOR ---
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        // Panel "Mis Alumnos" del profesor
        Route::get('/my-students', [TeacherController::class, 'myStudents'])->name('my-students');
        Route::post('/students/{student}/validate', [TeacherController::class, 'validateStudent'])->name('students.validate');
        Route::delete('/students/{student}', [TeacherController::class, 'destroyStudent'])->name('students.destroy');
        
        // Ver perfil público del alumno
        Route::get('/students/{student}', [TeacherController::class, 'show'])->name('students.show');
        
        // Ver candidaturas de un alumno
        Route::get('/students/{student}/applications', [TeacherController::class, 'applications'])->name('students.applications');
        
        // Ver matches de un alumno
        Route::get('/students/{student}/matches', [TeacherController::class, 'matches'])->name('students.matches');
        
        // Ver todas las candidaturas (global)
        Route::get('/applications', [TeacherController::class, 'allApplications'])->name('applications.index');
        
        // Matchings pendientes de validar
        Route::get('/matchings', [MatchingController::class, 'teacherIndex'])->name('matchings.index');
        Route::get('/matchings/{matching}', [MatchingController::class, 'teacherShow'])->name('matchings.show');
        Route::post('/matchings/{matching}/validate', [MatchingController::class, 'validateMatching'])->name('matchings.validate');
    });

// Zona de seguimiento (para profesores, alumnos y empresas autenticados)
Route::middleware(['auth'])->group(function () {
    Route::get('/seguimiento', [MatchingController::class, 'seguimiento'])->name('seguimiento.index');
    Route::get('/seguimiento/{matching}', [MatchingController::class, 'seguimientoShow'])->name('seguimiento.show');
    
    // Enviar mensajes en conversación
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('conversations.messages.store');
    
    // Subir y descargar documentos
    Route::post('/matchings/{matching}/documents', [DocumentController::class, 'store'])->name('matchings.documents.store');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    
    // Rutas de administración (solo admins)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de usuarios
        Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{type}/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{type}/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{type}/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        
        // Gestión de matchings
        Route::get('/matchings', [AdminController::class, 'indexMatchings'])->name('matchings.index');
        Route::patch('/matchings/{matching}/validation', [AdminController::class, 'updateMatchingValidation'])->name('matchings.validation');
        
        // Gestión de vacantes
        Route::get('/vacancies', [AdminController::class, 'indexVacancies'])->name('vacancies.index');
        Route::patch('/vacancies/{vacancy}/status', [AdminController::class, 'updateVacancyStatus'])->name('vacancies.status');
    });
});

// Rutas para editar/actualizar/borrar vacantes (empresa propietaria)
Route::middleware(['auth','role:company'])->group(function () {
    Route::get('/vacantes/{vacancy}/editar', [VacancyController::class, 'edit'])->name('vacancies.edit')->whereNumber('vacancy');
    Route::match(['put','patch'], '/vacantes/{vacancy}', [VacancyController::class, 'update'])->name('vacancies.update')->whereNumber('vacancy');
    Route::delete('/vacantes/{vacancy}', [VacancyController::class, 'destroy'])->name('vacancies.destroy')->whereNumber('vacancy');
});
});

// PÚBLICAS (perfiles)
Route::get('/students/public/{student}', [StudentController::class, 'publicShow'])->name('students.public.show');
Route::get('/companies/public/{company}', [CompanyController::class, 'publicShow'])->name('companies.public.show');

// Descargar CV de estudiante (accesible para usuarios autenticados)
Route::middleware('auth')->get('/students/{student}/cv', [App\Http\Controllers\StudentCvController::class, 'download'])->name('students.cv.download');

require __DIR__ . '/auth.php';
