<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentController extends Controller
{
    // Ficha pública (visible sin login).
    public function publicShow(Student $student)
    {
        $student->load('user');

        return Inertia::render('Students/PublicShow', [
            'student' => [
                'id'    => $student->id,
                'name'  => $student->user?->name ?? 'Alumno',
                'email' => Auth::check() ? ($student->user->email ?? null) : null,
            ],
        ]);
    }

    // Form de edición (si no existe Student para este user, lo creamos)
    public function edit(Request $request)
    {
        $user = $request->user();

        // Si el usuario aún no tiene fila en students, la creamos.
        if (!$user->student) {
            $user->loadMissing('student');
            $user->student = Student::firstOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $user->name] // ajusta si tu Student tiene otros fillables
            );
        }

        $student = $user->student->load('user');

        return Inertia::render('Students/Edit', [
            'student' => [
                'id'    => $student->id,
                'name'  => $student->user?->name,
                'email' => $student->user?->email, // lectura
            ],
        ]);
    }

    // Guardar cambios (por ahora SOLO name en users)
    public function update(Request $request)
    {
        $user = $request->user();

        // Asegura que el student existe también aquí
        if (!$user->student) {
            $user->student = Student::firstOrCreate(
                ['user_id' => $user->id],
                ['full_name' => $user->name]
            );
        }

        $data = $request->validate([
            'name' => ['required','string','max:255'],
        ]);

        $user->update(['name' => $data['name']]);

        return back()->with('status', 'Perfil actualizado');
    }
}
