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

        // Filtros (usar keys en inglés)
        $filters = $request->only(['cycle', 'mode', 'location']);

        // Compatibilidad >= 0 (mostrar todas con su porcentaje), pasando filtros
        $matches = $compatibility->forStudent($student, 0, $filters);

        // Transformamos a lo que espera Vacancies/Index.vue / CompatIndex.vue
        $items = $matches->map(function (array $row) {
            /** @var \App\Models\Vacancy $vacancy */
            $vacancy = $row['vacancy'];

            // Normalizar mode a español
            $mode = $vacancy->mode ?? $vacancy->modalidad ?? null;
            $mode = $this->normalizeMode($mode);

            return [
                'id'                => $vacancy->id,
                'title'             => $vacancy->title,
                'city'              => $vacancy->city,
                'province'          => $vacancy->province,
                'mode'              => $mode,
                'cycle_required'    => $vacancy->cycle_required,
                'availability_slot' => $vacancy->availability_slot,
                'created_at'        => optional($vacancy->created_at)->toDateTimeString(),
                'status'            => $vacancy->status,
                'company'           => $vacancy->company->trade_name
                    ?? $vacancy->company->legal_name
                    ?? $vacancy->company->name,
                'score'             => $row['score'] ?? 0,
                'breakdown'         => $row['breakdown'] ?? [],
            ];
        });

        // Paginación manual sobre la colección (se mantiene por compatibilidad)
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

        // Devolvemos también 'items' para CompatIndex.vue (array simple) y mantenemos 'vacantes' paginadas
        return Inertia::render('Vacancies/CompatIndex', [
            'items'    => $items->values()->all(),
            'vacantes' => $paginated,
            'filters'  => $filters,
        ]);
    }

    /**
     * Normalizar mode de inglés a español
     */
    private function normalizeMode(?string $mode): ?string
    {
        if (!$mode) return null;

        $normalized = strtolower(trim($mode));

        return match ($normalized) {
            'onsite', 'on-site' => 'presencial',
            'remote', 'teletrabajo' => 'remoto',
            'hybrid', 'hibrido', 'híbrido' => 'hibrido',
            default => $normalized,
        };
    }
}