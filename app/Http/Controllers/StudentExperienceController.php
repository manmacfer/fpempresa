<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExperienceController extends Controller
{
    public function store(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => Auth::id()]);

        $data = $request->validate([
            'company'      => ['required','string','max:190'],
            'position'     => ['nullable','string','max:190'],
            'start_date'   => ['nullable','date'],
            'end_date'     => ['nullable','date'],
            'functions'    => ['nullable','string'],
            'is_non_formal'=> ['nullable','boolean'],
        ]);

        $student->experiences()->create($data);

        return back()->with('success', 'Experiencia añadida.');
    }

    public function update(Request $request, StudentExperience $experience)
    {
        $this->ensureOwnerOrAbort($experience);

        $data = $request->validate([
            'company'      => ['required','string','max:190'],
            'position'     => ['nullable','string','max:190'],
            'start_date'   => ['nullable','date'],
            'end_date'     => ['nullable','date'],
            'functions'    => ['nullable','string'],
            'is_non_formal'=> ['nullable','boolean'],
        ]);

        $experience->update($data);

        return back()->with('success', 'Experiencia actualizada.');
    }

    public function destroy(StudentExperience $experience)
    {
        $this->ensureOwnerOrAbort($experience);
        $experience->delete();

        return back()->with('success', 'Experiencia eliminada.');
    }

    private function ensureOwnerOrAbort(StudentExperience $experience): void
    {
        if ($experience->student?->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
