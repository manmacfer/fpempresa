<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentController extends Controller
{
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

    public function edit(Request $request)
    {
        $student = optional($request->user()->student)->load('user');
        abort_if(!$student, 404);

        return Inertia::render('Students/Edit', [
            'student' => [
                'id'    => $student->id,
                'name'  => $student->user?->name,
                'email' => $student->user?->email, // solo lectura
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        abort_if(!$student, 404);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
        ]);

        $user->update(['name' => $data['name']]);

        return back()->with('status', 'Perfil actualizado');
    }
}
