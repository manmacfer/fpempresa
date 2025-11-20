<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Matching;
use App\Models\Vacancy;
use App\Models\Student;
use App\Models\Rejection;
use App\Models\Conversation;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    public function store(Request $request, Vacancy $vacancy, $studentId)
    {
        // Verifica si el estudiante existe
        $student = Student::find($studentId);
        if (!$student) {
            return back()->with('error', 'El estudiante no existe.');
        }

        // Verifica si ya existe un matching completo para esta vacante
        $existingCompleteMatch = Matching::where('vacancy_id', $vacancy->id)
            ->where('student_matched', true)
            ->where('company_matched', true)
            ->first();
            
        if ($existingCompleteMatch) {
            // Si ya existe un matching completo con otro estudiante
            if ($existingCompleteMatch->student_id !== $studentId) {
                return back()->with('error', 'Esta vacante ya tiene un Match completo con otro alumno.');
            }
            // Si el matching completo es con el mismo estudiante, ya está hecho
            return back()->with('info', 'Ya tienes un Match completo con este alumno.');
        }

        // Busca o crea el matching
        $matching = Matching::firstOrCreate(
            [
                'student_id' => $student->id,
                'company_id' => $vacancy->company_id,
                'vacancy_id' => $vacancy->id,
            ],
            [
                'status' => 'pending',
                'student_matched' => false,
                'company_matched' => false,
            ]
        );

        // Marca que la empresa dio match
        $matching->update(['company_matched' => true]);

        // Si ambos han dado match, crear la conversación y cambiar status a complete
        $matching->refresh();
        if ($matching->student_matched && $matching->company_matched) {
            // Actualizar status a complete
            $matching->update(['status' => 'complete']);
            
            Conversation::firstOrCreate([
                'matching_id' => $matching->id
            ]);
            
            // Notificar a todos los profesores del classroom si el estudiante tiene uno
            if ($student->classroom_id) {
                $classroom = $student->classroom;
                if ($classroom) {
                    foreach ($classroom->teachers as $teacher) {
                        NotificationService::newMatchingForTeacher($matching, $teacher);
                    }
                }
            }
        }

        // Elimina las demás candidaturas del alumno
        Application::where('student_id', $student->id)->delete();

        return back()->with('success', 'Has hecho Match con este alumno.');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Si es alumno, redirigir a la vista de candidaturas
        if ($user && $user->student) {
            return $this->student($request);
        }

        // Si es empresa, redirigir a la vista de empresa
        if ($user && $user->company) {
            return $this->company($request);
        }

        // Si no es alumno ni empresa, mostrar vacío
        return inertia('Matching/Student', [
            'applications' => [],
        ]);
    }

    /**
     * Vista de candidaturas (applications) para el alumno autenticado
     */
    public function student(Request $request)
    {
        $user = $request->user();
        
        if (!$user || !$user->student) {
            return inertia('Matching/Student', [
                'applications' => [],
            ]);
        }

        // Obtener todas las candidaturas del estudiante con sus relaciones
        $applications = Application::where('student_id', $user->student->id)
            ->with(['vacancy.company'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'vacancy_id' => $app->vacancy_id,
                    'student_id' => $app->student_id,
                    'status' => strtoupper($app->state ?? 'ENVIADA'), // Convertir a mayúsculas para consistencia
                    'created_at' => $app->created_at,
                    'vacancy' => $app->vacancy ? [
                        'id' => $app->vacancy->id,
                        'title' => $app->vacancy->title,
                        'description' => $app->vacancy->description,
                        'city' => $app->vacancy->city,
                        'province' => $app->vacancy->province,
                        'company' => $app->vacancy->company ? [
                            'trade_name' => $app->vacancy->company->trade_name,
                            'legal_name' => $app->vacancy->company->legal_name,
                        ] : null,
                    ] : null,
                ];
            });

        return inertia('Matching/Student', [
            'applications' => $applications,
        ]);
    }

    /**
     * Ver detalle de un matching (admin o usuarios autorizados)
     */
    public function show(Request $request, Matching $matching)
    {
        $user = $request->user();
        
        // Verificar acceso según rol
        if ($user->role !== 'admin') {
            // Si es profesor, verificar que pertenece a su classroom
            if ($user->role === 'teacher') {
                $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
                if (!$teacher || !$teacher->classroom_id || $matching->student->classroom_id !== $teacher->classroom_id) {
                    return redirect()->route('dashboard')
                        ->with('error', 'No tienes acceso a este matching.');
                }
            } 
            // Si es estudiante, verificar que es su matching
            elseif ($user->student && $matching->student_id !== $user->student->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes acceso a este matching.');
            }
            // Si es empresa, verificar que es su matching
            elseif ($user->company && $matching->company_id !== $user->company->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes acceso a este matching.');
            }
        }

        $matching->load([
            'student:id,user_id,first_name,last_name,cycle,avatar_path,city,province,phone,fp_modality',
            'student.user:id,email',
            'company:id,user_id,trade_name,legal_name,phone,city,province',
            'company.user:id,email',
            'vacancy:id,title,description,mode,city,province,cycle_required',
            'conversation.messages.user:id,name',
            'documents.uploader:id,name'
        ]);

        return inertia('Matchings/Show', [
            'matching' => $matching
        ]);
    }

    /**
     * Vista de matchings para la empresa autenticada
     */
    public function company(Request $request)
    {
        $user = $request->user();
        
        if (!$user || !$user->company) {
            return inertia('Matching/Company', [
                'vacancies' => [],
            ]);
        }

        // Obtener todas las vacantes de la empresa que NO tienen un matching completo
        $vacancies = Vacancy::where('company_id', $user->company->id)
            ->whereDoesntHave('matchings', function ($q) {
                $q->where('student_matched', true)
                  ->where('company_matched', true);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($vacancy) {
                // Obtener candidaturas para esta vacante
                $applications = Application::where('vacancy_id', $vacancy->id)
                    ->with('student.user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($app) {
                        $student = $app->student;
                        return [
                            'id' => $app->id,
                            'student_id' => $student->id,
                            'name' => trim($student->first_name . ' ' . $student->last_name),
                            'cycle' => $student->cycle,
                            'fp_modality' => $student->fp_modality,
                            'city' => $student->city,
                            'province' => $student->province,
                            'avatar_url' => $student->avatar_url,
                            'state' => $app->state,
                            'created_at' => $app->created_at,
                        ];
                    });

                return [
                    'id' => $vacancy->id,
                    'title' => $vacancy->title,
                    'applications' => $applications,
                ];
            });

        return inertia('Matching/Company', [
            'vacancies' => $vacancies,
        ]);
    }
    
    /**
     * Rechazar una candidatura (solo empresas)
     */
    public function reject(Request $request, Application $application)
    {
        $user = $request->user();
        
        // Verificar que el usuario es una empresa y que la vacante le pertenece
        if (!$user->company || $application->vacancy->company_id !== $user->company->id) {
            return back()->with('error', 'No tienes permiso para rechazar esta candidatura.');
        }
        
        // Crear registro de rechazo permanente
        Rejection::firstOrCreate([
            'vacancy_id' => $application->vacancy_id,
            'student_id' => $application->student_id,
        ], [
            'reason' => 'Rechazada por la empresa',
        ]);
        
        // Actualizar el estado a rechazada
        $application->update(['state' => 'rechazada']);
        
        return back()->with('success', 'Candidatura rechazada. El alumno no podrá volver a postularse.');
    }
    
    /**
     * Aceptar candidatura y crear Match definitivo (solo empresas)
     */
    public function accept(Request $request, Application $application)
    {
        $user = $request->user();
        
        // Verificar que el usuario es una empresa y que la vacante le pertenece
        if (!$user->company || $application->vacancy->company_id !== $user->company->id) {
            return back()->with('error', 'No tienes permiso para aceptar esta candidatura.');
        }
        
        // Verificar si la vacante ya tiene un matching completo
        if (Matching::where('vacancy_id', $application->vacancy_id)
            ->where('student_matched', true)
            ->where('company_matched', true)
            ->exists()) {
            return back()->with('error', 'Esta vacante ya tiene un Match completo.');
        }
        
        // Buscar si ya existe un matching entre este alumno y esta empresa
        $matching = Matching::where('student_id', $application->student_id)
            ->where('company_id', $application->vacancy->company_id)
            ->first();

        if ($matching) {
            // Si ya existe un matching con esta empresa, actualizar la vacancy_id si es diferente
            if ($matching->vacancy_id !== $application->vacancy_id) {
                $matching->update([
                    'vacancy_id' => $application->vacancy_id,
                    'company_matched' => true,
                    'student_matched' => false, // Resetear el match del estudiante ya que es una nueva vacante
                    'status' => 'pending'
                ]);
            } else {
                // Si es la misma vacante, solo marcar que la empresa dio match
                $matching->update(['company_matched' => true]);
            }
        } else {
            // Si no existe, crear un nuevo matching
            $matching = Matching::create([
                'student_id' => $application->student_id,
                'company_id' => $application->vacancy->company_id,
                'vacancy_id' => $application->vacancy_id,
                'status' => 'pending',
                'student_matched' => false,
                'company_matched' => true,
            ]);
        }
        
        // Si ambos han dado match, crear la conversación y cambiar status a complete
        $matching->refresh();
        if ($matching->student_matched && $matching->company_matched) {
            // Actualizar status a complete
            $matching->update(['status' => 'complete']);
            
            $conversation = Conversation::where('matching_id', $matching->id)->first();
            if (!$conversation) {
                $conversation = new Conversation();
                $conversation->matching_id = $matching->id;
                $conversation->save();
            }

            // Notificar al estudiante que su candidatura fue aceptada
            NotificationService::matchingCreated($matching->student, $matching);

            // Notificar a todos los profesores del classroom si el estudiante tiene uno
            if ($matching->student->classroom_id) {
                $classroom = $matching->student->classroom;
                if ($classroom) {
                    foreach ($classroom->teachers as $teacher) {
                        NotificationService::newMatchingForTeacher($matching, $teacher);
                    }
                }
            }
        }
        
        // Actualizar estado de esta candidatura a aceptada
        $application->update(['state' => 'aceptada']);
        
        // Eliminar las demás candidaturas del alumno
        Application::where('student_id', $application->student_id)
            ->where('id', '!=', $application->id)
            ->delete();
        
        return back()->with('success', 'Match realizado con éxito. Se han eliminado las demás candidaturas del alumno.');
    }

    /**
     * Vista de matchings pendientes de validar (solo profesores)
     */
    public function teacherIndex(Request $request)
    {
        $user = $request->user();
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

        if (!$teacher || !$teacher->classroom_id) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un classroom asignado.');
        }

        // Obtener matches completos pendientes de validación solo del classroom del profesor
        $matchings = Matching::with([
                'student:id,user_id,first_name,last_name,cycle,avatar_path,classroom_id',
                'student.user:id,email',
                'company:id,user_id,trade_name,legal_name',
                'vacancy:id,title,mode,city,province'
            ])
            ->whereHas('student', function($q) use ($teacher) {
                $q->where('classroom_id', $teacher->classroom_id);
            })
            ->pendingValidation()
            ->orderBy('created_at', 'desc')
            ->get();

        return inertia('Teacher/Matchings/Index', [
            'matchings' => $matchings
        ]);
    }

    /**
     * Ver detalle de un matching (solo profesores)
     */
    public function teacherShow(Request $request, Matching $matching)
    {
        $user = $request->user();
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

        if (!$teacher || !$teacher->classroom_id) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un classroom asignado.');
        }

        // Verificar que el alumno del matching pertenece al classroom del profesor
        if ($matching->student->classroom_id !== $teacher->classroom_id) {
            return redirect()->route('teacher.matchings.index')
                ->with('error', 'Este matching no pertenece a tu classroom.');
        }

        $matching->load([
            'student:id,user_id,first_name,last_name,cycle,avatar_path,city,province,phone',
            'student.user:id,email',
            'company:id,user_id,trade_name,legal_name,phone,city,province',
            'company.user:id,email',
            'vacancy:id,title,description,mode,city,province,cycle_required',
            'conversation.messages.user:id,name',
            'documents.uploader:id,name'
        ]);

        return inertia('Teacher/Matchings/Show', [
            'matching' => $matching
        ]);
    }

    /**
     * Validar un matching (solo profesores)
     */
    public function validateMatching(Request $request, Matching $matching)
    {
        $user = $request->user();
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

        if (!$teacher || !$teacher->classroom_id) {
            return back()->with('error', 'No tienes un classroom asignado.');
        }

        // Verificar que el alumno del matching pertenece al classroom del profesor
        if ($matching->student->classroom_id !== $teacher->classroom_id) {
            return back()->with('error', 'Este matching no pertenece a tu classroom.');
        }

        $request->validate([
            'comentarios' => 'nullable|string|max:1000'
        ]);

        // Verificar que el matching está completo
        if (!$matching->student_matched || !$matching->company_matched) {
            return back()->with('error', 'Este matching no está completo aún.');
        }

        // Verificar que no está ya validado
        if ($matching->validado_centro) {
            return back()->with('error', 'Este matching ya está validado.');
        }

        // Validar el matching
        $matching->update([
            'validado_centro' => true,
            'comentarios_centro' => $request->comentarios
        ]);

        // Crear conversación si no existe
        if (!$matching->conversation) {
            Conversation::firstOrCreate([
                'matching_id' => $matching->id
            ]);
        }

        return back()->with('success', 'Matching validado correctamente. Ahora está disponible en la Zona de Seguimiento.');
    }

    /**
     * Zona de seguimiento - Lista de matchings validados (profesores, alumnos, empresas)
     */
    public function seguimiento(Request $request)
    {
        $user = $request->user();
        
        $query = Matching::with([
                'student:id,user_id,first_name,last_name,cycle,avatar_path,classroom_id',
                'student.user:id,email',
                'company:id,user_id,trade_name,legal_name',
                'vacancy:id,title',
                'conversation'
            ])
            ->validated();

        // Filtrar según el rol
        if ($user->student) {
            $query->where('student_id', $user->student->id);
        } elseif ($user->company) {
            $query->where('company_id', $user->company->id);
        } elseif ($user->role === 'teacher') {
            // Si es profesor, solo ve matchings de su classroom
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            if ($teacher && $teacher->classroom_id) {
                $query->whereHas('student', function($q) use ($teacher) {
                    $q->where('classroom_id', $teacher->classroom_id);
                });
            } else {
                // Si no tiene classroom, no ve nada
                $query->whereRaw('1 = 0');
            }
        }
        // Si es admin, ve todos

        $matchings = $query->orderBy('updated_at', 'desc')->get();

        return inertia('Seguimiento/Index', [
            'matchings' => $matchings
        ]);
    }

    /**
     * Ver detalle de un matching en zona de seguimiento
     */
    public function seguimientoShow(Request $request, Matching $matching)
    {
        // Verificar que el matching está validado
        if (!$matching->validado_centro) {
            return redirect()->route('dashboard')
                ->with('error', 'Este matching aún no ha sido validado.');
        }

        $user = $request->user();
        
        // Verificar acceso según rol
        if ($user->role === 'teacher') {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            if (!$teacher || !$teacher->classroom_id || $matching->student->classroom_id !== $teacher->classroom_id) {
                return redirect()->route('seguimiento.index')
                    ->with('error', 'No tienes acceso a este matching.');
            }
        } elseif ($user->student && $matching->student_id !== $user->student->id) {
            return redirect()->route('seguimiento.index')
                ->with('error', 'No tienes acceso a este matching.');
        } elseif ($user->company && $matching->company_id !== $user->company->id) {
            return redirect()->route('seguimiento.index')
                ->with('error', 'No tienes acceso a este matching.');
        }

        $matching->load([
            'student:id,user_id,first_name,last_name,cycle,avatar_path,city,province,phone,fp_modality',
            'student.user:id,email',
            'company',
            'company.user:id,email',
            'vacancy:id,title,description,mode,city,province',
            'conversation.messages.user:id,name',
            'documents.uploader:id,name'
        ]);

        return inertia('Seguimiento/Show', [
            'matching' => $matching
        ]);
    }
}
