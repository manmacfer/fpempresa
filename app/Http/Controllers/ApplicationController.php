<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\Student;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $r, Vacancy $vacancy)
    {
        abort_unless(auth()->user()->isAlumno(), 403);
        $student = Student::firstOrCreate(['user_id' => auth()->id()], [
            'full_name' => auth()->user()->name,
            'ciclo' => 'DAW'
        ]);
        Application::firstOrCreate([
            'vacancy_id' => $vacancy->id,
            'student_id' => $student->id
        ]);
        return back()->with('success', 'Solicitud enviada.');
    }
}
