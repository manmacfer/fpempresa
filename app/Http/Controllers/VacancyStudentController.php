<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VacancyStudentController extends Controller
{
    public function index(Request $request, CompatibilityService $compatibility)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->role !== 'student') {
            abort(403);
        }

        /** @var Student|null $student */
        $student = $user->student; // ajusta al nombre real de la relación si fuera otro

        // Por seguridad: si por lo que sea no tiene perfil de alumno asociado
        if (! $student) {
            abort(403);
            // o, si prefieres, podrías hacer un redirect a la página de completar perfil
        }

        // Vacantes con compatibilidad >= 50
        $matches = $compatibility->forStudent($student, 50);

        return Inertia::render('Vacancies/Index', [
            'vacancies' => $matches
                ->map(function (array $row) {
                    /** @var \App\Models\Vacancy $vacancy */
                    $vacancy = $row['vacancy'];

                    return [
                        'id'        => $vacancy->id,
                        'title'     => $vacancy->title,
                        'company'   => $vacancy->company->trade_name
                            ?? $vacancy->company->legal_name
                            ?? $vacancy->company->name,
                        'city'      => $vacancy->city,
                        'modalidad' => $vacancy->modalidad,
                        'status'    => $vacancy->status,
                        'score'     => $row['score'],
                        'breakdown' => $row['breakdown'],
                    ];
                })
                ->values(), // para que llegue a Vue como array indexado
        ]);
    }
}
