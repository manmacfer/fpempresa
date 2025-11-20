<?php
namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Matching;
use App\Models\Vacancy;
use App\Models\Rejection;
use App\Models\Conversation;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, Vacancy $vacancy)
    {
        $user = $request->user();

        // Verifica si el usuario tiene un perfil de estudiante
        if (!$user || !$user->student) {
            return back()->with('error', 'No tienes un perfil de estudiante.');
        }

        $student = $user->student;

        // Verifica si la vacante ya tiene un matching completo con otro alumno
        if (Matching::where('vacancy_id', $vacancy->id)
            ->where('student_matched', true)
            ->where('company_matched', true)
            ->exists()) {
            return back()->with('error', 'Esta vacante ya tiene un Match completo con otro alumno.');
        }

        // Verifica si el alumno ya tiene un matching completo
        if (Matching::where('student_id', $student->id)
            ->where('student_matched', true)
            ->where('company_matched', true)
            ->exists()) {
            return back()->with('error', 'Ya tienes un Match completo activo.');
        }

        // Verifica si el alumno fue rechazado previamente de esta vacante
        if (Rejection::where('student_id', $student->id)->where('vacancy_id', $vacancy->id)->exists()) {
            return back()->with('error', 'No puedes postularte a esta vacante.');
        }
        
        // Verifica si ya existe una candidatura para esta vacante
        if (Application::where('student_id', $student->id)->where('vacancy_id', $vacancy->id)->exists()) {
            return back()->with('error', 'Ya has dado Match a esta vacante.');
        }

        // Crea la candidatura
        $application = Application::create([
            'student_id' => $student->id,
            'vacancy_id' => $vacancy->id,
            'state' => 'enviada',
        ]);

        // Notificar a la empresa
        NotificationService::newApplication($application);

        // Notificar a los profesores del classroom si el estudiante tiene uno
        if ($student->classroom_id && $student->classroom) {
            foreach ($student->classroom->teachers as $teacher) {
                NotificationService::newApplicationForTeacher($application, $teacher);
            }
        }

        // Buscar si ya existe un matching entre este alumno y esta empresa
        $matching = Matching::where('student_id', $student->id)
            ->where('company_id', $vacancy->company_id)
            ->first();

        if ($matching) {
            // Si ya existe un matching con esta empresa, actualizar la vacancy_id y resetear los estados si es diferente
            if ($matching->vacancy_id !== $vacancy->id) {
                $matching->update([
                    'vacancy_id' => $vacancy->id,
                    'student_matched' => true,
                    'company_matched' => false, // Resetear el match de la empresa ya que es una nueva vacante
                    'status' => 'pending'
                ]);
            } else {
                // Si es la misma vacante, solo marcar que el estudiante dio match
                $matching->update(['student_matched' => true]);
            }
        } else {
            // Si no existe, crear un nuevo matching
            $matching = Matching::create([
                'student_id' => $student->id,
                'company_id' => $vacancy->company_id,
                'vacancy_id' => $vacancy->id,
                'status' => 'pending',
                'student_matched' => true,
                'company_matched' => false,
            ]);
        }

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

        return back()->with('success', 'Has dado Match a esta vacante.');
    }

    /**
     * Cancelar/eliminar una candidatura
     */
    public function destroy(Request $request, Application $application)
    {
        $user = $request->user();

        // Verificar que el usuario es el dueño de la candidatura
        if (!$user || !$user->student || $application->student_id !== $user->student->id) {
            return back()->with('error', 'No tienes permiso para eliminar esta candidatura.');
        }

        // Si la candidatura está rechazada, mantener el registro de rechazo
        if ($application->state === 'rechazada') {
            // El registro de rejection ya existe, solo eliminamos la application
            $application->delete();
            return back()->with('success', 'Candidatura eliminada. No podrás volver a postularte a esta vacante.');
        }

        $application->delete();

        return back()->with('success', 'Candidatura cancelada correctamente.');
    }
}