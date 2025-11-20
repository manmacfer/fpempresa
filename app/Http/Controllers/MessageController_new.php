<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Enviar un mensaje en una conversación
     */
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        // Verificar que el usuario puede participar en esta conversación
        $conversation->load(['matching.student.user', 'matching.student.teacher.user', 'matching.company.user']);
        $matching = $conversation->matching;
        $user = $request->user();

        $canParticipate = false;
        
        if ($user->student && $matching->student_id === $user->student->id) {
            $canParticipate = true;
        } elseif ($user->company && $matching->company_id === $user->company->id) {
            $canParticipate = true;
        } elseif ($user->teacher) {
            // El profesor puede participar en todas las conversaciones
            $canParticipate = true;
        }

        if (!$canParticipate) {
            return back()->with('error', 'No tienes permiso para enviar mensajes en esta conversación.');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $request->input('content')
        ]);

        // Cargar relaciones necesarias para las notificaciones
        $message->load(['sender.student', 'sender.company', 'sender.teacher', 'conversation']);

        // Debug: Log para verificar qué está pasando
        Log::info('MessageController - Enviando notificaciones', [
            'message_id' => $message->id,
            'sender_id' => $user->id,
            'matching_id' => $matching->id,
            'has_student' => $matching->student ? 'yes' : 'no',
            'has_company' => $matching->company ? 'yes' : 'no',
            'has_teacher' => ($matching->student && $matching->student->teacher) ? 'yes' : 'no',
            'student_user_id' => $matching->student?->user_id,
            'company_user_id' => $matching->company?->user_id,
            'teacher_user_id' => $matching->student?->teacher?->user_id,
        ]);

        // Notificar a los otros participantes
        // Estudiante recibe si el mensaje es de empresa o profesor
        if ($matching->student && $matching->student->user && $matching->student->user_id !== $user->id) {
            Log::info('Enviando notificación a estudiante', ['user_id' => $matching->student->user_id]);
            NotificationService::newMessage($message, $matching->student->user);
        }

        // Empresa recibe si el mensaje es de estudiante o profesor
        if ($matching->company && $matching->company->user && $matching->company->user_id !== $user->id) {
            Log::info('Enviando notificación a empresa', ['user_id' => $matching->company->user_id]);
            NotificationService::newMessage($message, $matching->company->user);
        }

        // Profesor recibe si existe y el mensaje es de estudiante o empresa
        if ($matching->student->teacher && $matching->student->teacher->user && $matching->student->teacher->user_id !== $user->id) {
            Log::info('Enviando notificación a profesor', ['user_id' => $matching->student->teacher->user_id]);
            NotificationService::newMessage($message, $matching->student->teacher->user);
        }

        return back()->with('success', 'Mensaje enviado correctamente.');
    }
}
