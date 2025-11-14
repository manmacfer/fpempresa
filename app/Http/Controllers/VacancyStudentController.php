<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class VacancyStudentController extends Controller
{
    public function index(Request $request, CompatibilityService $compatibility)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        /** @var Student|null $student */
        $student = $user->student; // relación hasOne en User

        if (! $student) {
            abort(403);
        }

        // Filtros que ya usa tu vista (de momento solo los devolvemos tal cual)
        $filters = $request->only(['ciclo', 'modalidad', 'ubicacion']);

        // Compatibilidad >= 50
        $matches = $compatibility->forStudent($student, 50);

        // Transformamos a lo que espera Vacancies/Index.vue
        $items = $matches->map(function (array $row) {
            /** @var \App\Models\Vacancy $vacancy */
            $vacancy = $row['vacancy'];

            return [
                'id'              => $vacancy->id,
                'title'           => $vacancy->title,
                'city'            => $vacancy->city,
                'province'        => $vacancy->province,
                'ubicacion'       => $vacancy->ubicacion,
                'modalidad'       => $vacancy->modalidad,     // PRESENCIAL/HIBRIDA/REMOTA
                'mode'            => $vacancy->mode,           // horario preferente (mañana/tarde…)
                'ciclo_requerido' => $vacancy->cycle_required,
                'cycle_required'  => $vacancy->cycle_required,
                'created_at'      => optional($vacancy->created_at)->toDateTimeString(),
                'status'          => $vacancy->status,
                'company'         => $vacancy->company->trade_name
                    ?? $vacancy->company->legal_name
                    ?? $vacancy->company->name,
                'score'           => $row['score'],
                'breakdown'       => $row['breakdown'],
            ];
        });

        // (Opcional) aquí podríamos aplicar filtros sobre $items si quieres que sigan funcionando,
        // pero de momento lo dejamos tal cual para centrarnos en que salga la compatibilidad.

        // Paginación manual sobre la colección
        $page    = (int) $request->input('page', 1);
        $perPage = 9;

        $paginated = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return Inertia::render('Vacancies/Index', [
            'vacantes' => $paginated,
            'filters'  => $filters,
        ]);
    }
}
