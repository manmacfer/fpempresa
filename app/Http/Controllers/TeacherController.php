<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Vacancy;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TeacherController extends Controller
{
    protected $compatibilityService;

    public function __construct(CompatibilityService $compatibilityService)
    {
        $this->compatibilityService = $compatibilityService;
    }

    /**
     * Lista de alumnos con filtros por ciclo
     */
    public function index(Request $request)
    {
        $query = Student::query()
            ->select(['id', 'user_id', 'first_name', 'last_name', 'cycle', 'fp_modality', 'city', 'province', 'avatar_path'])
            ->with(['user:id,email'])
            ->orderBy('first_name');

        // Filtro por ciclo
        if ($request->filled('cycle')) {
            $cycle = $request->input('cycle');
            $query->where('cycle', 'LIKE', "%{$cycle}%");
        }

        // Filtro por búsqueda general
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('cycle', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->paginate(20);

        // Obtener lista de ciclos únicos para el filtro
        $cycles = Student::query()
            ->select('cycle')
            ->whereNotNull('cycle')
            ->where('cycle', '!=', '')
            ->distinct()
            ->orderBy('cycle')
            ->pluck('cycle');

        return Inertia::render('Teachers/Students/Index', [
            'students' => $students,
            'cycles' => $cycles,
            'filters' => $request->only(['cycle', 'search'])
        ]);
    }

    /**
     * Panel "Mis Alumnos" - Solo los alumnos del classroom del profesor
     */
    public function myStudents(Request $request)
    {
        $user = $request->user();
        Log::info('myStudents - User:', ['user_id' => $user->id, 'role' => $user->role]);
        
        // Cargar explícitamente el teacher con todos los campos
        $teacher = Teacher::where('user_id', $user->id)->first();
        Log::info('myStudents - Teacher:', ['teacher' => $teacher ? $teacher->toArray() : 'NULL']);

        if (!$teacher || !$teacher->classroom_id) {
            Log::warning('myStudents - Sin classroom', ['teacher' => $teacher, 'classroom_id' => $teacher?->classroom_id]);
            return back()->with('error', 'No tienes un classroom asignado.');
        }

        // Obtener solo los alumnos de su classroom
        $query = Student::query()
            ->where('classroom_id', $teacher->classroom_id)
            ->with(['user:id,email'])
            ->orderByRaw('validated ASC, first_name ASC'); // No validados primero

        // Filtro por estado de validación
        if ($request->filled('status')) {
            if ($request->input('status') === 'pending') {
                $query->where('validated', false);
            } elseif ($request->input('status') === 'validated') {
                $query->where('validated', true);
            }
        }

        $students = $query->paginate(20);

        // Obtener info del classroom
        $classroom = $teacher->classroom;

        return Inertia::render('Teachers/MyStudents', [
            'students' => $students,
            'classroom' => $classroom,
            'filters' => $request->only(['status'])
        ]);
    }

    /**
     * Validar un alumno
     */
    public function validateStudent(Request $request, Student $student)
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        
        Log::info('validateStudent - Teacher:', ['teacher' => $teacher ? $teacher->toArray() : 'NULL']);
        Log::info('validateStudent - Student:', ['student_id' => $student->id, 'classroom_id' => $student->classroom_id]);

        // Verificar que el alumno pertenece al classroom del profesor
        if (!$teacher || $student->classroom_id !== $teacher->classroom_id) {
            Log::warning('validateStudent - Rechazo', [
                'teacher_classroom' => $teacher?->classroom_id,
                'student_classroom' => $student->classroom_id
            ]);
            return back()->with('error', 'Este alumno no pertenece a tu classroom.');
        }

        $student->update(['validated' => true]);
        Log::info('validateStudent - Éxito', ['student_id' => $student->id]);

        return back()->with('success', 'Alumno validado correctamente.');
    }

    /**
     * Eliminar un alumno pendiente de validación
     */
    public function destroyStudent(Request $request, Student $student)
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Verificar que el alumno pertenece al classroom del profesor
        if (!$teacher || $student->classroom_id !== $teacher->classroom_id) {
            return back()->with('error', 'Este alumno no pertenece a tu classroom.');
        }

        // Solo se pueden eliminar alumnos no validados
        if ($student->validated) {
            return back()->with('error', 'No puedes eliminar un alumno ya validado.');
        }

        // Eliminar el usuario asociado (cascade eliminará el student)
        $student->user->delete();

        return back()->with('success', 'Alumno eliminado correctamente.');
    }

    /**
     * Ver perfil público del alumno
     */
    public function show(Request $request, Student $student)
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Verificar que el alumno pertenece al classroom del profesor
        if (!$teacher || $student->classroom_id !== $teacher->classroom_id) {
            return redirect()->route('teacher.my-students')
                ->with('error', 'Este alumno no pertenece a tu classroom.');
        }

        $student->load([
            'user',
            'educations',
            'experiences',
            'languages'
        ]);

        return Inertia::render('Teachers/Students/Show', [
            'student' => $student
        ]);
    }

    /**
     * Ver todas las candidaturas del alumno
     */
    public function applications(Request $request, Student $student)
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Verificar que el alumno pertenece al classroom del profesor
        if (!$teacher || $student->classroom_id !== $teacher->classroom_id) {
            return redirect()->route('teacher.my-students')
                ->with('error', 'Este alumno no pertenece a tu classroom.');
        }

        $student->load([
            'user',
            'applications.vacancy.company'
        ]);

        return Inertia::render('Teachers/Students/Applications', [
            'student' => $student,
            'applications' => $student->applications
        ]);
    }

    /**
     * Ver todos los matches de un alumno con % de compatibilidad
     */
    public function matches(Request $request, Student $student)
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Verificar que el alumno pertenece al classroom del profesor
        if (!$teacher || $student->classroom_id !== $teacher->classroom_id) {
            return redirect()->route('teacher.my-students')
                ->with('error', 'Este alumno no pertenece a tu classroom.');
        }

        // Cargar datos completos del estudiante
        $student->load([
            'user',
            'educations',
            'experiences',
            'languages'
        ]);

        // Obtener todas las vacantes publicadas
        $vacancies = Vacancy::query()
            ->with(['company', 'requiredLanguages'])
            ->where('status', 'published')
            ->get();

        // Calcular compatibilidad para cada vacante
        $matches = $vacancies->map(function (Vacancy $vacancy) use ($student) {
            $scoreData = $this->compatibilityService->scoreStudentForVacancy($student, $vacancy);

            return [
                'vacancy' => $vacancy,
                'score' => $scoreData['score'],
                'eligible' => $scoreData['eligible'],
                'breakdown' => $scoreData['breakdown'],
            ];
        })
        ->filter(function ($match) {
            return $match['eligible'];
        })
        ->sortByDesc('score')
        ->values();

        return Inertia::render('Teachers/Students/Matches', [
            'student' => $student,
            'matches' => $matches
        ]);
    }

    /**
     * Ver todas las candidaturas de todos los alumnos
     */
    public function allApplications(Request $request)
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher || !$teacher->classroom_id) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes un classroom asignado.');
        }

        $query = \App\Models\Application::query()
            ->with([
                'student:id,user_id,first_name,last_name,avatar_path,classroom_id',
                'student.user:id,email',
                'vacancy:id,title,company_id',
                'vacancy.company:id,legal_name,trade_name'
            ])
            // Solo candidaturas de alumnos del classroom del profesor
            ->whereHas('student', function($q) use ($teacher) {
                $q->where('classroom_id', $teacher->classroom_id);
            })
            ->orderBy('created_at', 'desc');

        // Filtro por estado
        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        // Filtro por búsqueda (alumno o vacante)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($sq) use ($search) {
                    $sq->where('first_name', 'LIKE', "%{$search}%")
                       ->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('vacancy', function($vq) use ($search) {
                    $vq->where('title', 'LIKE', "%{$search}%");
                });
            });
        }

        $applications = $query->paginate(20);

        return Inertia::render('Teachers/Applications/Index', [
            'applications' => $applications,
            'filters' => $request->only(['state', 'search'])
        ]);
    }
}
