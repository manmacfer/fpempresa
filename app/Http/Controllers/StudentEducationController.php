<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentEducation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentEducationController extends Controller
{
    public function store(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => Auth::id()]);

        $data = $request->validate([
            'title'      => ['required','string','max:190'],
            'center'     => ['nullable','string','max:190'],
            'start_date' => ['nullable','date'],
            'end_date'   => ['nullable','date'],
        ]);

        $student->educations()->create($data);

        return back()->with('success', 'Formación añadida.');
    }

    public function update(Request $request, StudentEducation $education)
    {
        $this->ensureOwnerOrAbort($education);

        $data = $request->validate([
            'title'      => ['required','string','max:190'],
            'center'     => ['nullable','string','max:190'],
            'start_date' => ['nullable','date'],
            'end_date'   => ['nullable','date'],
        ]);

        $education->update($data);

        return back()->with('success', 'Formación actualizada.');
    }

    public function destroy(StudentEducation $education)
    {
        $this->ensureOwnerOrAbort($education);
        $education->delete();

        return back()->with('success', 'Formación eliminada.');
    }

    private function ensureOwnerOrAbort(StudentEducation $education): void
    {
        if ($education->student?->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
