<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Company;
use App\Models\Matching;
use App\Models\Vacancy;
use App\Models\Application;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Mostrar el dashboard del administrador
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => Student::count(),
            'total_companies' => Company::count(),
            'total_teachers' => Teacher::count(),
            'total_vacancies' => Vacancy::count(),
            'active_vacancies' => Vacancy::where('status', 'published')->count(),
            'total_matchings' => Matching::count(),
            'validated_matchings' => Matching::where('validado_centro', true)->count(),
            'pending_applications' => Application::where('state', 'enviada')->count(),
        ];

        return inertia('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }

    /**
     * Mostrar formulario de creación de usuarios
     */
    public function createUser()
    {
        // Obtener classrooms existentes para el formulario de profesores
        $classrooms = \App\Models\Classroom::select('id', 'nombre', 'classroom_number')
            ->orderBy('classroom_number')
            ->get();

        return Inertia::render('Admin/Users/Create', [
            'classrooms' => $classrooms
        ]);
    }

    /**
     * Crear un nuevo usuario
     */
    public function storeUser(Request $request)
    {
        // Validación base
        $rules = [
            'role' => 'required|in:student,company,teacher,admin',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'first_name' => 'required_if:role,student,teacher|nullable|string|max:100',
            'last_name' => 'required_if:role,student,teacher|nullable|string|max:150',
            'company_name' => 'required_if:role,company|nullable|string|max:255',
            // Campos específicos de alumno
            'cycle' => 'required_if:role,student|nullable|string|max:50',
            'center' => 'required_if:role,student|nullable|string|max:255',
            // Campos específicos de empresa
            'trade_name' => 'nullable|string|max:255',
            'sector' => 'required_if:role,company|nullable|string|max:100',
            // Campos específicos para profesor
            'classroom_action' => 'required_if:role,teacher|nullable|in:new,existing',
        ];

        // Validación condicional para classroom
        if ($request->role === 'teacher') {
            if ($request->classroom_action === 'existing') {
                $rules['classroom_id'] = 'required|exists:classrooms,id';
            } elseif ($request->classroom_action === 'new') {
                $rules['classroom_number'] = 'required|string|max:50|unique:classrooms,classroom_number';
                $rules['classroom_name'] = 'required|string|max:255';
            }
        }

        $validated = $request->validate($rules);

        // Crear usuario
        $user = User::create([
            'name' => $validated['role'] === 'company' 
                ? $validated['company_name'] 
                : ($validated['first_name'] ?? 'Admin') . ' ' . ($validated['last_name'] ?? ''),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'role_id' => match($validated['role']) {
                'admin' => 1,
                'teacher' => 2,
                'student' => 3,
                'company' => 4,
                default => null
            },
            'email_verified_at' => now(),
        ]);

        // Crear perfil según el rol
        if ($validated['role'] === 'student') {
            Student::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'cycle' => $validated['cycle'] ?? null,
                'center' => $validated['center'] ?? null,
                'validated' => true, // Los alumnos creados por admin están pre-validados
            ]);
        } elseif ($validated['role'] === 'teacher') {
            Log::info('Creando profesor. classroom_action:', ['action' => $validated['classroom_action'] ?? 'NO DEFINIDO']);
            
            // Manejar classroom según la acción
            if ($validated['classroom_action'] === 'new') {
                Log::info('Creando nuevo classroom:', [
                    'nombre' => $validated['classroom_name'] ?? 'NO DEFINIDO',
                    'classroom_number' => $validated['classroom_number'] ?? 'NO DEFINIDO'
                ]);
                
                // Crear nuevo classroom (sin teacher_id todavía)
                $classroom = \App\Models\Classroom::create([
                    'nombre' => $validated['classroom_name'],
                    'classroom_number' => $validated['classroom_number'],
                ]);
                
                Log::info('Classroom creado:', ['id' => $classroom->id]);
                $classroomId = $classroom->id;
            } else {
                Log::info('Usando classroom existente:', ['classroom_id' => $validated['classroom_id']]);
                // Usar classroom existente
                $classroomId = $validated['classroom_id'];
            }

            // Crear profesor
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'full_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'classroom_id' => $classroomId,
            ]);
            
            Log::info('Profesor creado:', ['id' => $teacher->id, 'classroom_id' => $classroomId]);

            // Actualizar el teacher_id del classroom si se creó uno nuevo
            if (isset($classroom)) {
                $classroom->update(['teacher_id' => $teacher->id]);
                Log::info('Classroom actualizado con teacher_id:', ['classroom_id' => $classroom->id, 'teacher_id' => $teacher->id]);
            }
        } elseif ($validated['role'] === 'company') {
            Company::create([
                'user_id' => $user->id,
                'name' => $validated['company_name'],
                'trade_name' => $validated['trade_name'] ?? $validated['company_name'],
                'sector' => $validated['sector'] ?? null,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Listar usuarios por tipo
     */
    public function indexUsers(Request $request)
    {
        $type = $request->query('type', 'student');

        $users = collect();

        if ($type === 'student') {
            $users = Student::with('user:id,email,created_at')
                ->select('id', 'user_id', 'first_name', 'last_name', 'cycle', 'center', 'city', 'province')
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'type' => 'student',
                        'first_name' => $student->first_name,
                        'last_name' => $student->last_name,
                        'email' => $student->user->email,
                        'cycle' => $student->cycle,
                        'center' => $student->center,
                        'city' => $student->city,
                        'province' => $student->province,
                        'created_at' => $student->user->created_at,
                    ];
                });
        } elseif ($type === 'company') {
            $users = Company::with('user:id,email,created_at')
                ->select('id', 'user_id', 'name', 'trade_name', 'sector', 'city', 'province')
                ->get()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'type' => 'company',
                        'name' => $company->trade_name ?? $company->name,
                        'email' => $company->user->email,
                        'sector' => $company->sector,
                        'city' => $company->city,
                        'province' => $company->province,
                        'created_at' => $company->user->created_at,
                    ];
                });
        } elseif ($type === 'teacher') {
            $users = Teacher::with('user:id,email,created_at')
                ->select('id', 'user_id', 'full_name')
                ->get()
                ->map(function ($teacher) {
                    return [
                        'id' => $teacher->id,
                        'type' => 'teacher',
                        'full_name' => $teacher->full_name,
                        'email' => $teacher->user->email,
                        'created_at' => $teacher->user->created_at,
                    ];
                });
        }

        return inertia('Admin/Users/Index', [
            'users' => $users,
            'type' => $type
        ]);
    }

    /**
     * Mostrar formulario de edición de usuario
     */
    public function editUser($type, $id)
    {
        $userData = null;

        if ($type === 'student') {
            $student = Student::with('user')->findOrFail($id);
            $userData = [
                'id' => $student->id,
                'type' => 'student',
                'email' => $student->user->email,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'cycle' => $student->cycle,
                'city' => $student->city,
                'province' => $student->province,
            ];
        } elseif ($type === 'company') {
            $company = Company::with('user')->findOrFail($id);
            $userData = [
                'id' => $company->id,
                'type' => 'company',
                'email' => $company->user->email,
                'company_name' => $company->trade_name ?? $company->name,
                'sector' => $company->sector,
                'city' => $company->city,
                'province' => $company->province,
            ];
        } elseif ($type === 'teacher') {
            $teacher = Teacher::with('user')->findOrFail($id);
            $userData = [
                'id' => $teacher->id,
                'type' => 'teacher',
                'email' => $teacher->user->email,
                'full_name' => $teacher->full_name,
            ];
        }

        return inertia('Admin/Users/Edit', [
            'user' => $userData,
            'type' => $type
        ]);
    }

    /**
     * Actualizar un usuario
     */
    public function updateUser(Request $request, $type, $id)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'nullable|min:8',
        ];

        if ($type === 'student') {
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:150';
        } elseif ($type === 'company') {
            $rules['company_name'] = 'required|string|max:255';
        } elseif ($type === 'teacher') {
            $rules['full_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        if ($type === 'student') {
            $student = Student::findOrFail($id);
            $user = $student->user;
            
            $student->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
            ]);

            $user->update([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }
        } elseif ($type === 'company') {
            $company = Company::findOrFail($id);
            $user = $company->user;

            $company->update([
                'trade_name' => $validated['company_name'],
                'name' => $validated['company_name'],
            ]);

            $user->update([
                'name' => $validated['company_name'],
                'email' => $validated['email'],
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }
        } elseif ($type === 'teacher') {
            $teacher = Teacher::findOrFail($id);
            $user = $teacher->user;

            $teacher->update([
                'full_name' => $validated['full_name'],
            ]);

            $user->update([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }
        }

        return redirect()->route('admin.users.index', ['type' => $type])->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar un usuario
     */
    public function destroyUser($type, $id)
    {
        if ($type === 'student') {
            $student = Student::findOrFail($id);
            $user = $student->user;
            
            // Eliminar relaciones
            Matching::where('student_id', $student->id)->delete();
            Application::where('student_id', $student->id)->delete();
            
            $student->delete();
            $user->delete();
        } elseif ($type === 'company') {
            $company = Company::findOrFail($id);
            $user = $company->user;
            
            // Eliminar relaciones
            Vacancy::where('company_id', $company->id)->delete();
            Matching::where('company_id', $company->id)->delete();
            Application::whereHas('vacancy', function($q) use ($company) {
                $q->where('company_id', $company->id);
            })->delete();
            
            $company->delete();
            $user->delete();
        } elseif ($type === 'teacher') {
            $teacher = Teacher::findOrFail($id);
            $user = $teacher->user;
            
            $teacher->delete();
            $user->delete();
        }

        return redirect()->route('admin.users.index', ['type' => $type])->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Listar todos los matchings
     */
    public function indexMatchings(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Matching::with([
            'student:id,user_id,first_name,last_name,cycle,city,province',
            'student.user:id,email',
            'company:id,user_id,name,trade_name,sector,city,province',
            'company.user:id,email',
            'vacancy:id,title',
            'conversation'
        ]);

        // Filtros
        if ($status === 'pending') {
            $query->where('validado_centro', false);
        } elseif ($status === 'validated') {
            $query->where('validado_centro', true);
        } elseif ($status === 'incomplete') {
            $query->where(function($q) {
                $q->where('student_matched', false)
                  ->orWhere('company_matched', false);
            });
        } elseif ($status === 'complete') {
            $query->where('student_matched', true)
                  ->where('company_matched', true);
        }

        $matchings = $query->orderBy('created_at', 'desc')->get();

        return inertia('Admin/Matchings/Index', [
            'matchings' => $matchings,
            'status' => $status
        ]);
    }

    /**
     * Validar/invalidar un matching
     */
    public function updateMatchingValidation(Request $request, Matching $matching)
    {
        $validated = $request->validate([
            'validado_centro' => 'required|boolean',
            'comentarios_centro' => 'nullable|string|max:1000'
        ]);

        $wasValidated = $matching->validado_centro;
        $matching->update($validated);

        // Si se está validando (cambio de false a true), enviar notificaciones
        if (!$wasValidated && $validated['validado_centro']) {
            NotificationService::matchingValidated($matching);
        }

        return back()->with('success', 'Matching actualizado correctamente.');
    }

    /**
     * Listar todas las vacantes
     */
    public function indexVacancies(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Vacancy::with([
            'company:id,user_id,name,trade_name,sector',
            'company.user:id,email'
        ]);

        // Filtros
        if ($status === 'published') {
            $query->where('status', 'published');
        } elseif ($status === 'closed') {
            $query->where('status', 'closed');
        }

        $vacancies = $query->orderBy('created_at', 'desc')->get();

        // Contar candidaturas por vacante
        $vacancies = $vacancies->map(function ($vacancy) {
            $vacancy->applications_count = Application::where('vacancy_id', $vacancy->id)->count();
            return $vacancy;
        });

        return inertia('Admin/Vacancies/Index', [
            'vacancies' => $vacancies,
            'status' => $status
        ]);
    }

    /**
     * Cambiar estado de una vacante
     */
    public function updateVacancyStatus(Request $request, Vacancy $vacancy)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,published,closed'
        ]);

        $vacancy->update($validated);

        if ($validated['status'] === 'published') {
            $vacancy->update(['published_at' => now()]);
        }

        return back()->with('success', 'Estado de la vacante actualizado correctamente.');
    }
}
