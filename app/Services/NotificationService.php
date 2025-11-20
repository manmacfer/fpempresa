<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Crear una notificación para un usuario
     */
    public static function create(
        User $user,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?array $data = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data,
        ]);
    }

    /**
     * Notificación: Matching creado (para estudiante)
     */
    public static function matchingCreated($student, $matching): void
    {
        self::create(
            user: $student->user,
            type: 'matching_created',
            title: '¡Tu candidatura ha sido aceptada!',
            message: "La empresa {$matching->vacancy->company->company_name} ha aceptado tu candidatura para {$matching->vacancy->title}",
            link: route('matchings.show', $matching->id),
            data: ['matching_id' => $matching->id]
        );
    }

    /**
     * Notificación: Matching validado por el centro (para estudiante y empresa)
     */
    public static function matchingValidated($matching): void
    {
        // Para el estudiante
        self::create(
            user: $matching->student->user,
            type: 'matching_validated',
            title: 'Matching validado por tu centro',
            message: "Tu centro ha validado el matching con {$matching->vacancy->company->company_name}",
            link: route('matchings.show', $matching->id),
            data: ['matching_id' => $matching->id]
        );

        // Para la empresa
        self::create(
            user: $matching->vacancy->company->user,
            type: 'matching_validated',
            title: 'Matching validado por el centro',
            message: "El centro educativo ha validado el matching con {$matching->student->first_name} {$matching->student->last_name}",
            link: route('matchings.show', $matching->id),
            data: ['matching_id' => $matching->id]
        );
    }

    /**
     * Notificación: Nueva candidatura (para empresa)
     */
    public static function newApplication($application): void
    {
        self::create(
            user: $application->vacancy->company->user,
            type: 'new_application',
            title: 'Nueva candidatura recibida',
            message: "{$application->student->first_name} {$application->student->last_name} ha aplicado a tu vacante: {$application->vacancy->title}",
            link: route('vacancies.show', $application->vacancy->id),
            data: [
                'application_id' => $application->id,
                'vacancy_id' => $application->vacancy->id
            ]
        );
    }

    /**
     * Notificación: Nuevo matching pendiente de validar (para profesor)
     */
    public static function newMatchingForTeacher($matching, $teacher): void
    {
        self::create(
            user: $teacher->user,
            type: 'matching_pending_validation',
            title: 'Nuevo matching pendiente de validación',
            message: "{$matching->student->first_name} {$matching->student->last_name} tiene un matching completo con {$matching->vacancy->company->trade_name} pendiente de validar",
            link: route('teacher.matchings.index'),
            data: ['matching_id' => $matching->id]
        );
    }

    /**
     * Notificación: Nueva candidatura de estudiante (para profesor)
     */
    public static function newApplicationForTeacher($application, $teacher): void
    {
        self::create(
            user: $teacher->user,
            type: 'new_application',
            title: 'Tu estudiante ha aplicado a una vacante',
            message: "{$application->student->first_name} {$application->student->last_name} ha aplicado a: {$application->vacancy->title}",
            link: route('students.show', $application->student->id),
            data: [
                'application_id' => $application->id,
                'student_id' => $application->student->id
            ]
        );
    }

    /**
     * Notificación: Nuevo mensaje en conversación
     */
    public static function newMessage($message, $recipient): void
    {
        $sender = $message->sender;
        $conversation = $message->conversation;
        
        // Determinar el nombre del remitente
        if ($sender->student) {
            $senderName = $sender->student->first_name . ' ' . $sender->student->last_name;
        } elseif ($sender->company) {
            $senderName = $sender->company->trade_name ?? $sender->company->legal_name;
        } elseif ($sender->teacher) {
            $senderName = $sender->teacher->full_name . ' (Profesor)';
        } else {
            $senderName = $sender->name ?? 'Usuario';
        }

        self::create(
            user: $recipient,
            type: 'new_message',
            title: 'Nuevo mensaje',
            message: "{$senderName} te ha enviado un mensaje",
            link: route('seguimiento.show', ['matching' => $conversation->matching_id]),
            data: [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'matching_id' => $conversation->matching_id
            ]
        );
    }
}
