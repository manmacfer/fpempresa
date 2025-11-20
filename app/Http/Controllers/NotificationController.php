<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Obtener las notificaciones del usuario autenticado
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'is_read' => $notification->isRead(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = $request->user()->notifications()->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        // Verificar que la notificación pertenece al usuario
        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(Request $request, Notification $notification)
    {
        // Verificar que la notificación pertenece al usuario
        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }
}
