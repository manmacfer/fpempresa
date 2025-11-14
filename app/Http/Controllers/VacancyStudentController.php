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

        if ($user->role !== 'student') {
            abort(403);
        }

        /** @var Student|null $student */
        $student = $user->student; // ajusta al nombre real de la relación si fuera otro

        // Si no tiene perfil de alumno asociado
        if (! $student) {
            abort(403);
        }

        // Filtros del listado
        $filters = $request->only(['ciclo', 'modalidad', 'ubicacion']);

        // Compatibilidad >= 50 (el servicio se encargará de aplicar filtros)
        $matches = $compatibility->forStudent($student, 50, $filters);

        // Transformamos a DTOs que encajan con el Index.vue
        $items = $matches->map(function (array $row) {
            /** @var \App\Models\Vacancy $vacancy */
            $vacancy = $row['vacancy'];

            return [
                'id'              => $vacancy->id,
                'title'           => $vacancy->title,
                'city'            => $vacancy->city,
                'province'        => $vacancy->province ?? null,
                'ubicacion'       => $vacancy->ubicacion ?? null,
                'modalidad'       => $vacancy->modalidad,
                'mode'            => $vacancy->mode ?? null,
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

        // Paginación "a mano" sobre la colección
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
