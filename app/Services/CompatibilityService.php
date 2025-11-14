<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Vacancy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompatibilityService
{
    // Pesos (suman 100)
    private const WEIGHT_TECH      = 45;
    private const WEIGHT_CYCLE_FP  = 20;
    private const WEIGHT_SCHEDULE  = 10;
    private const WEIGHT_MODE      = 10;
    private const WEIGHT_LOCATION  = 5;
    private const WEIGHT_LANG      = 10;

    private const DEFAULT_MIN_SCORE = 50;

    /**
     * Calcula compatibilidad para TODAS las vacantes publicadas/abiertas
     * para un alumno, devolviendo solo las de score >= $minScore.
     */
    public function forStudent(Student $student, int $minScore = self::DEFAULT_MIN_SCORE): Collection
    {
        $vacancies = Vacancy::query()
            ->where('status', 'published')
            ->where('estado_vacante', 'ABIERTA')
            ->with('company')
            ->get();

        if ($vacancies->isEmpty()) {
            return collect();
        }

        // Contexto del alumno (una sola vez)
        $studentCompetencies = $this->loadStudentCompetencies($student->id);
        $studentLanguages    = $this->loadStudentLanguages($student->id);

        // Contexto de vacantes (también en bloque)
        $vacancyIds          = $vacancies->pluck('id');
        $vacancyCompetencies = $this->loadVacancyCompetencies($vacancyIds);
        $vacancyLanguages    = $this->loadVacancyLanguages($vacancyIds);

        $results = [];

        foreach ($vacancies as $vacancy) {
            $scoreData = $this->scoreWithContext(
                $student,
                $vacancy,
                $studentCompetencies,
                $studentLanguages,
                $vacancyCompetencies[$vacancy->id] ?? [],
                $vacancyLanguages[$vacancy->id] ?? [],
            );

            if ($scoreData['eligible'] && $scoreData['score'] >= $minScore) {
                $results[] = array_merge(
                    ['vacancy' => $vacancy],
                    $scoreData,
                );
            }
        }

        return collect($results)
            ->sortByDesc('score')
            ->values();
    }

    /**
     * Calcula compatibilidad de un alumno concreto con UNA vacante.
     * Útil para "detalle" en la vista de la vacante.
     */
    public function score(Student $student, Vacancy $vacancy): array
    {
        $studentCompetencies = $this->loadStudentCompetencies($student->id);
        $studentLanguages    = $this->loadStudentLanguages($student->id);

        $vacCompetencies = $this->loadVacancyCompetencies(collect([$vacancy->id]));
        $vacLanguages    = $this->loadVacancyLanguages(collect([$vacancy->id]));

        return $this->scoreWithContext(
            $student,
            $vacancy,
            $studentCompetencies,
            $studentLanguages,
            $vacCompetencies[$vacancy->id] ?? [],
            $vacLanguages[$vacancy->id] ?? [],
        );
    }

    /**
     * Implementación central del cálculo, recibiendo todos los datos ya cargados.
     */
    protected function scoreWithContext(
        Student $student,
        Vacancy $vacancy,
        array $studentCompetencies,
        array $studentLanguages,
        array $vacancyCompetencies,
        array $vacancyLanguages,
    ): array {
        // Solo puntuamos vacantes publicadas y abiertas
        if ($vacancy->status !== 'published' || $vacancy->estado_vacante !== 'ABIERTA') {
            return [
                'eligible'  => false,
                'score'     => 0,
                'breakdown' => [],
            ];
        }

        // 1) Filtros must: idiomas requeridos
        if (!$this->checkRequiredLanguages($studentLanguages, $vacancyLanguages)) {
            return [
                'eligible'  => false,
                'score'     => 0,
                'breakdown' => [],
            ];
        }

        // 2) Filtro must: competencias técnicas obligatorias
        $eligible = true;
        $techFactor = $this->scoreTech($studentCompetencies, $vacancyCompetencies, $eligible);

        if (!$eligible) {
            return [
                'eligible'  => false,
                'score'     => 0,
                'breakdown' => [],
            ];
        }

        // 3) Resto de bloques (escala 0–1)
        $cycleFactor     = $this->scoreCycleAndFp($student, $vacancy);
        $scheduleFactor  = $this->scoreSchedule($student, $vacancy);
        $modeFactor      = $this->scoreMode($student, $vacancy);
        $locationFactor  = $this->scoreLocation($student, $vacancy);
        $langFactor      = $this->scoreLanguages($studentLanguages, $vacancyLanguages);

        // 4) Montamos breakdown ya ponderado
        $scores = [
            'tech'       => (int) round(self::WEIGHT_TECH     * $techFactor),
            'cycle_fp'   => (int) round(self::WEIGHT_CYCLE_FP * $cycleFactor),
            'schedule'   => (int) round(self::WEIGHT_SCHEDULE * $scheduleFactor),
            'mode'       => (int) round(self::WEIGHT_MODE     * $modeFactor),
            'location'   => (int) round(self::WEIGHT_LOCATION * $locationFactor),
            'languages'  => (int) round(self::WEIGHT_LANG     * $langFactor),
        ];

        $total = array_sum($scores);

        return [
            'eligible'  => true,
            'score'     => $total,
            'breakdown' => $scores,
        ];
    }

    /* =====================================================
     *  CARGA DE DATOS (BD) — pivotes alumno/vacante
     * ===================================================== */

    protected function loadStudentCompetencies(int $studentId): array
    {
        return DB::table('alumno_competencia')
            ->where('student_id', $studentId)
            ->pluck('competency_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int,string> [language_id => nivel ('A2','B1',...)]
     */
    protected function loadStudentLanguages(int $studentId): array
    {
        return DB::table('alumno_idioma')
            ->where('student_id', $studentId)
            ->get(['language_id', 'nivel'])
            ->mapWithKeys(function ($row) {
                return [(int) $row->language_id => (string) $row->nivel];
            })
            ->all();
    }

    /**
     * @return array<int,array<int,array{competency_id:int, es_obligatoria:bool}>>
     */
    protected function loadVacancyCompetencies(Collection $vacancyIds): array
    {
        if ($vacancyIds->isEmpty()) {
            return [];
        }

        return DB::table('vacante_competencia_req')
            ->whereIn('vacancy_id', $vacancyIds->all())
            ->get()
            ->groupBy('vacancy_id')
            ->map(function (Collection $rows) {
                return $rows->map(function ($row) {
                    return [
                        'competency_id'  => (int) $row->competency_id,
                        'es_obligatoria' => (bool) $row->es_obligatoria,
                    ];
                })->all();
            })
            ->all();
    }

    /**
     * @return array<int,array<int,array{language_id:int, min_level:?string, required:bool}>>
     */
    protected function loadVacancyLanguages(Collection $vacancyIds): array
    {
        if ($vacancyIds->isEmpty()) {
            return [];
        }

        return DB::table('vacante_idioma_req')
            ->whereIn('vacancy_id', $vacancyIds->all())
            ->get()
            ->groupBy('vacancy_id')
            ->map(function (Collection $rows) {
                return $rows->map(function ($row) {
                    return [
                        'language_id' => (int) $row->language_id,
                        'min_level'   => $row->min_level ?? $row->nivel_minimo,
                        'required'    => (bool) $row->required,
                    ];
                })->all();
            })
            ->all();
    }

    /* =====================================================
     *  BLOQUES DE SCORE (devuelven 0–1)
     * ===================================================== */

    /**
     * Competencias técnicas.
     * - Si falta alguna obligatoria => $eligible = false.
     * - Cobertura = (# competencias de vacante que tiene el alumno / # totales de vacante).
     */
    protected function scoreTech(array $studentCompetencies, array $vacancyCompetencies, bool &$eligible): float
    {
        if (empty($vacancyCompetencies)) {
            return 0.0;
        }

        $requiredIds = [];
        $allReqIds   = [];

        foreach ($vacancyCompetencies as $row) {
            $cid = (int) $row['competency_id'];
            $allReqIds[] = $cid;

            if (!empty($row['es_obligatoria'])) {
                $requiredIds[] = $cid;
            }
        }

        $studentSet = array_unique($studentCompetencies);

        // Must-have
        foreach ($requiredIds as $cid) {
            if (!in_array($cid, $studentSet, true)) {
                $eligible = false;
                return 0.0;
            }
        }

        $allReqIds = array_unique($allReqIds);

        if (empty($allReqIds)) {
            return 0.0;
        }

        $intersect = array_intersect($allReqIds, $studentSet);

        return count($intersect) / count($allReqIds);
    }

    /**
     * Ciclo y tipo de FP: si ciclo coincide y la modalidad es aceptada por la vacante,
     * se lleva el bloque entero; si no, 0.
     */
    protected function scoreCycleAndFp(Student $student, Vacancy $vacancy): float
    {
        $studentCycle  = $this->normalizeCycle($student->cycle ?? $student->ciclo);
        $vacancyCycle  = $this->normalizeCycle($vacancy->cycle_required);
        $studentFpMode = $student->fp_modality;

        if (!$studentCycle || !$vacancyCycle) {
            return 0.0;
        }

        if ($studentCycle !== $vacancyCycle) {
            return 0.0;
        }

        $acceptsGeneral = property_exists($vacancy, 'accepts_fp_general')
            ? (bool) $vacancy->accepts_fp_general
            : true;

        $acceptsDual = property_exists($vacancy, 'accepts_fp_dual')
            ? (bool) $vacancy->accepts_fp_dual
            : true;

        $fp = $studentFpMode ? Str::lower($studentFpMode) : null;

        if ($fp === 'general' && !$acceptsGeneral) {
            return 0.0;
        }

        if ($fp === 'dual' && !$acceptsDual) {
            return 0.0;
        }

        // Si no hay modalidad de FP en el alumno o la vacante acepta ambas, damos el bloque completo.
        return 1.0;
    }

    /**
     * Disponibilidad real (franja mañana/tarde/ambas).
     */
    protected function scoreSchedule(Student $student, Vacancy $vacancy): float
    {
        $studentSlot = $this->normalizeSlot($student->availability_slot);
        $vacancySlot = $this->normalizeSlot($vacancy->mode);

        if (!$studentSlot || !$vacancySlot) {
            return 0.0;
        }

        if ($studentSlot === 'both') {
            // Alumno flexible
            return 1.0;
        }

        if ($studentSlot === $vacancySlot) {
            return 1.0;
        }

        // Si la vacante es "flexible" o "split" y el alumno tiene al menos una franja:
        if (in_array($vacancySlot, ['flexible', 'split'], true)) {
            return 0.7;
        }

        return 0.0;
    }

    /**
     * Modalidad de trabajo (presencial/remoto/híbrido).
     */
    protected function scoreMode(Student $student, Vacancy $vacancy): float
    {
        $studentMode = $this->normalizeModality($student->work_modality);
        $vacancyMode = $this->normalizeModality($vacancy->modalidad);

        if (!$studentMode || !$vacancyMode) {
            return 0.0;
        }

        if ($studentMode === $vacancyMode) {
            return 1.0;
        }

        // Híbrida vs presencial/remoto: algo de encaje
        if ($vacancyMode === 'hybrid' && in_array($studentMode, ['onsite', 'remote'], true)) {
            return 0.7;
        }

        return 0.0;
    }

    /**
     * Ubicación (localidad alumno vs municipio vacante).
     * Si coincide ciudad → 100% de este bloque.
     * Si no coincide pero el alumno se puede desplazar o la vacante es remota → 50%.
     */
    protected function scoreLocation(Student $student, Vacancy $vacancy): float
    {
        $studentCity = $this->normalizeCity($student->city);
        $vacancyCity = $this->normalizeCity($vacancy->city);

        if (!$studentCity || !$vacancyCity) {
            return 0.0;
        }

        if ($studentCity === $vacancyCity) {
            return 1.0;
        }

        $relocate    = (bool) $student->relocate;
        $vacancyMode = $this->normalizeModality($vacancy->modalidad);

        if ($relocate || $vacancyMode === 'remote') {
            return 0.5;
        }

        return 0.0;
    }

    /**
     * Comprueba que los idiomas marcados como required se cumplen.
     */
    protected function checkRequiredLanguages(array $studentLangs, array $vacancyLangs): bool
    {
        foreach ($vacancyLangs as $row) {
            if (empty($row['required'])) {
                continue;
            }

            $langId    = (int) $row['language_id'];
            $reqLevel  = $this->mapLanguageLevel($row['min_level'] ?? null);
            $studentLv = isset($studentLangs[$langId])
                ? $this->mapLanguageLevel($studentLangs[$langId])
                : null;

            if (!$reqLevel) {
                // Si no hay nivel definido, no lo tratamos como filtro duro.
                continue;
            }

            if (!$studentLv || $studentLv < $reqLevel) {
                return false;
            }
        }

        return true;
    }

    /**
     * Idiomas: media de "ratios" por idioma requerido.
     * - >= nivel mínimo → 1.0
     * - un escalón por debajo → 0.5
     * - resto → 0
     */
    protected function scoreLanguages(array $studentLangs, array $vacancyLangs): float
    {
        if (empty($vacancyLangs)) {
            return 0.0;
        }

        $ratios = [];

        foreach ($vacancyLangs as $row) {
            $langId = (int) $row['language_id'];
            $req    = $this->mapLanguageLevel($row['min_level'] ?? null);

            if (!$req) {
                continue;
            }

            $studentLevel = isset($studentLangs[$langId])
                ? $this->mapLanguageLevel($studentLangs[$langId])
                : null;

            if (!$studentLevel) {
                $ratios[] = 0.0;
                continue;
            }

            if ($studentLevel >= $req) {
                $ratios[] = 1.0;
            } elseif ($studentLevel + 1 === $req) {
                $ratios[] = 0.5;
            } else {
                $ratios[] = 0.0;
            }
        }

        if (empty($ratios)) {
            return 0.0;
        }

        return array_sum($ratios) / count($ratios);
    }

    /* =====================================================
     *  HELPERS DE NORMALIZACIÓN
     * ===================================================== */

    protected function normalizeCycle(?string $cycle): ?string
    {
        if (!$cycle) {
            return null;
        }

        $c = Str::upper(trim($cycle));
        $c = preg_replace('/\s+/', ' ', $c);

        return $c ?: null;
    }

    protected function normalizeSlot(?string $slot): ?string
    {
        if (!$slot) {
            return null;
        }

        $s = Str::ascii(Str::lower(trim($slot))); // "mañana" -> "manana"

        return match ($s) {
            'manana', 'morning'         => 'morning',
            'tarde', 'afternoon'        => 'afternoon',
            'ambas', 'ambos', 'both'    => 'both',
            'partido', 'split'          => 'split',
            'flexible'                  => 'flexible',
            default                     => null,
        };
    }

    protected function normalizeModality(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $v = Str::ascii(Str::lower(trim($value)));

        if (in_array($v, ['presencial', 'onsite', 'oficina'], true)) {
            return 'onsite';
        }

        if (in_array($v, ['remota', 'remote'], true)) {
            return 'remote';
        }

        if (in_array($v, ['hibrida', 'híbrida', 'hybrid', 'mixta'], true)) {
            return 'hybrid';
        }

        return null;
    }

    protected function normalizeCity(?string $city): ?string
    {
        if (!$city) {
            return null;
        }

        $c = Str::ascii(Str::lower(trim($city)));

        return $c ?: null;
    }

    protected function mapLanguageLevel(?string $level): ?int
    {
        if (!$level) {
            return null;
        }

        return match (Str::upper(trim($level))) {
            'A1' => 1,
            'A2' => 2,
            'B1' => 3,
            'B2' => 4,
            'C1' => 5,
            'C2' => 6,
            default => null,
        };
    }
}
