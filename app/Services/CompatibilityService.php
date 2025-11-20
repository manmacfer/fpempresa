<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Vacancy;
use Illuminate\Support\Collection;

class CompatibilityService
{
    /**
     * Vacantes publicadas para un alumno, con score >= $minScore,
     * ordenadas por compatibilidad desc.
     */
    public function forStudent(Student $student, int $minScore = 0, array $filters = []): Collection
    {
        // Asegurarse de que el estudiante está completamente cargado
        $student = $student->fresh();

        $query = Vacancy::query()
            ->with(['company', 'requiredLanguages'])
            ->where('status', 'published')
            // Excluir vacantes que ya tienen un matching completo
            ->whereDoesntHave('matchings', function ($q) {
                $q->where('student_matched', true)
                  ->where('company_matched', true);
            });

        // Filtros (ciclo, modalidad, ubicación)
        if (! empty($filters['cycle'])) {
            $value = trim($filters['cycle']);
            $query->where(function ($q) use ($value) {
                $q->where('cycle_required', 'LIKE', "%{$value}%")
                    ->orWhereNull('cycle_required');
            });
        }

        if (! empty($filters['mode'])) {
            $mod = trim($filters['mode']);
            $query->where('mode', $mod);
        }

        if (! empty($filters['location'])) {
            $ubi = trim($filters['location']);
            $query->where(function ($q) use ($ubi) {
                $q->where('city', 'LIKE', "%{$ubi}%")
                    ->orWhere('province', 'LIKE', "%{$ubi}%");
            });
        }

        $vacancies = $query->get();

        $results = $vacancies
            ->map(function (Vacancy $vacancy) use ($student) {
                $scoreData = $this->scoreStudentForVacancy($student, $vacancy);

                return array_merge($scoreData, [
                    'vacancy' => $vacancy,
                ]);
            })
            ->filter(function (array $row) use ($minScore) {
                return ($row['eligible'] ?? false) && ($row['score'] ?? 0) >= $minScore;
            })
            ->sortByDesc('score')
            ->values();

        return $results;
    }

    /**
     * Calcula el score 0–100 para un alumno concreto y una vacante.
     */
    public function scoreStudentForVacancy(Student $student, Vacancy $vacancy): array
    {
        // 1) Regla dura: solo vacantes publicadas
        if ($vacancy->status !== 'published') {
            return [
                'eligible'  => false,
                'score'     => 0,
                'breakdown' => [],
            ];
        }

        // 2) Ciclo + tipo FP (puede dejar la vacante como no elegible)
        [$cycleFpScore, $cycleFpBreakdown, $eligibleCycleFp] =
            $this->scoreCycleAndFp($student, $vacancy);

        if (! $eligibleCycleFp) {
            return [
                'eligible'  => false,
                'score'     => 0,
                'breakdown' => [
                    'cycle_fp' => 0,
                ],
            ];
        }

        // 3) Resto de bloques
        $availabilityScore = $this->scoreAvailability($student, $vacancy); // 0..1
        $modalityScore     = $this->scoreModalities($student, $vacancy);   // 0..1
        $locationScore     = $this->scoreLocation($student, $vacancy);     // 0..1
        $techScore         = $this->scoreTechnologies($student, $vacancy); // 0..1
        $langScore         = $this->scoreLanguages($student, $vacancy);    // 0..1

        // 4) Pesos de cada bloque (suma 100)
        $weights = [
            'cycle_fp'     => 25,
            'availability' => 5,
            'modality'     => 10,
            'location'     => 10,
            'tech'         => 40,
            'languages'    => 10,
        ];

        $total =
            $cycleFpScore      * $weights['cycle_fp']
            + $availabilityScore * $weights['availability']
            + $modalityScore     * $weights['modality']
            + $locationScore     * $weights['location']
            + $techScore         * $weights['tech']
            + $langScore         * $weights['languages'];

        return [
            'eligible' => true,
            'score'    => (int) round($total),
            'breakdown' => [
                'cycle_fp'     => (int) round($cycleFpScore * $weights['cycle_fp']),
                'availability' => (int) round($availabilityScore * $weights['availability']),
                'modality'     => (int) round($modalityScore * $weights['modality']),
                'location'     => (int) round($locationScore * $weights['location']),
                'tech'         => (int) round($techScore * $weights['tech']),
                'languages'    => (int) round($langScore * $weights['languages']),
                'raw'          => [
                    'cycle_fp'     => $cycleFpBreakdown,
                    'availability' => $availabilityScore,
                    'modality'     => $modalityScore,
                    'location'     => $locationScore,
                    'tech'         => $techScore,
                    'languages'    => $langScore,
                ],
            ],
        ];
    }

    /**
     * Ciclo + tipo de FP.
     * - 2º DAM cuenta como válido para vacante que pide 1º DAM (sin extra).
     * - Si familia no coincide, o el alumno está por debajo del curso requerido → no elegible.
     */
    protected function scoreCycleAndFp(Student $student, Vacancy $vacancy): array
    {
        $studentCycle = $this->parseCycle($student->cycle ?? null);
        $vacCycle     = $this->parseCycle($vacancy->cycle_required ?? null);

        $cyclePart = 1.0;
        $eligible  = true;

        if ($vacCycle) {
            // La vacante pide algo concreto
            if (! $studentCycle) {
                // Alumno sin ciclo rellenado → no entra en esta vacante
                return [0.0, ['cycle' => 0.0, 'fp' => 0.0], false];
            }

            // Familia distinta (DAM vs DAW) → fuera
            if ($studentCycle['family'] !== $vacCycle['family']) {
                return [0.0, ['cycle' => 0.0, 'fp' => 0.0], false];
            }

            // Curso por debajo → fuera (1º para vacante que pide 2º)
            if ($studentCycle['year'] < $vacCycle['year']) {
                return [0.0, ['cycle' => 0.0, 'fp' => 0.0], false];
            }

            // 1º==1º, 2º==2º, 2º para vacante 1º → OK, sin extra
            $cyclePart = 1.0;
        }

        // Tipo de FP
        $fpPart = 1.0;
        $studentFp = strtolower((string) ($student->fp_modality ?? ''));
        $acceptsGeneral = (bool) ($vacancy->accepts_fp_general ?? false);
        $acceptsDual    = (bool) ($vacancy->accepts_fp_dual ?? false);

        if ($studentFp) {
            if ($studentFp === 'general') {
                if (! $acceptsGeneral) {
                    return [0.0, ['cycle' => $cyclePart, 'fp' => 0.0], false];
                }
                $fpPart = 1.0;
            } elseif ($studentFp === 'dual') {
                if (! $acceptsDual) {
                    return [0.0, ['cycle' => $cyclePart, 'fp' => 0.0], false];
                }
                $fpPart = 1.0;
            } else {
                $fpPart = 0.0;
            }
        } else {
            // No ha rellenado tipo de FP → no lo hacemos no elegible, pero no suma por este subapartado
            $fpPart = 0.0;
        }

        // 60% ciclo, 40% tipo FP dentro del bloque de 25 puntos
        $score = 0.6 * $cyclePart + 0.4 * $fpPart;

        return [$score, ['cycle' => $cyclePart, 'fp' => $fpPart], $eligible];
    }

    /**
     * Normaliza ciclos tipo "1 DAM", "2DAW" → ['year' => 1|2, 'family' => 'DAM'|'DAW']
     */
    protected function parseCycle(?string $cycle): ?array
    {
        if (! $cycle) {
            return null;
        }

        $cycle = strtoupper(str_replace(['º', 'ª'], '', $cycle));
        $cycle = preg_replace('/\s+/', '', $cycle); // "2 DAW" -> "2DAW"

        if (! preg_match('/^([12])?(DAM|DAW)$/', $cycle, $m)) {
            return null;
        }

        $year   = isset($m[1]) ? (int) $m[1] : null;
        $family = $m[2];

        // Si solo ponen "DAM" / "DAW", asumimos 2º para que no excluya vacantes de 1º
        if ($year === null) {
            $year = 2;
        }

        return ['year' => $year, 'family' => $family];
    }

    /* ===================== DISPONIBILIDAD ===================== */

    protected function scoreAvailability(Student $student, Vacancy $vacancy): float
    {
        $studentSlot = $this->normalizeSlot($student->availability_slot ?? null);
        $vacSlots    = $this->normalizeVacancySlots($vacancy->availability_slot ?? null);

        if (! $studentSlot || empty($vacSlots)) {
            return 0.0;
        }

        // REGLA 1: Si el alumno tiene COMPLETA, es compatible con cualquier franja (mañana, tarde o completa)
        if ($studentSlot === 'COMPLETA') {
            return 1.0;
        }

        // REGLA 2: Si el alumno tiene MAÑANA o TARDE, solo es compatible con su misma franja
        // (NO es compatible con COMPLETA, porque no puede cubrir todo el horario)
        return in_array($studentSlot, $vacSlots, true) ? 1.0 : 0.0;
    }

    protected function normalizeSlot(?string $slot): ?string
    {
        if (! $slot) return null;
        $v = mb_strtolower(trim($slot));
        if (str_contains($v, 'mañana') || str_starts_with($v, 'm')) return 'MANANA';
        if (str_contains($v, 'tarde') || str_starts_with($v, 't')) return 'TARDE';
        if (str_contains($v, 'completa') || str_contains($v, 'amb')) return 'COMPLETA';
        return null;
    }

    protected function normalizeVacancySlots($value): array
    {
        if (is_string($value)) {
            $slot = $this->normalizeSlot($value);
            return $slot ? [$slot] : [];
        }

        // Si es array o json, sigue como antes
        $list = $this->normalizeList($value);
        $out  = [];

        foreach ($list as $item) {
            $slot = $this->normalizeSlot($item);
            if ($slot) {
                $out[$slot] = true;
            }
        }

        return array_keys($out);
    }

    /* ===================== MODALIDAD (multi alumno) ===================== */

    protected function scoreModalities(Student $student, Vacancy $vacancy): float
    {
        // Si el estudiante tiene modalidad 'indiferente', acepta cualquier modalidad
        $rawStudentMod = $student->work_modality ?? null;
        if ($rawStudentMod && strtolower(trim($rawStudentMod)) === 'indiferente') {
            return 1.0;
        }

        $studentMods = $this->studentModalities($student);
        $vacMod      = $this->normalizeModality($vacancy->mode ?? null);

        if (! $vacMod || empty($studentMods)) {
            return 0.0;
        }

        return in_array($vacMod, $studentMods, true) ? 1.0 : 0.0;
    }

    protected function studentModalities(Student $student): array
    {
        $raw = $student->work_modalities ?? $student->work_modality ?? null;

        if (is_string($raw)) {
            $parts = explode(',', $raw);
        } elseif (is_array($raw)) {
            $parts = $raw;
        } else {
            $parts = [];
        }

        $set = [];
        foreach ($parts as $p) {
            $m = $this->normalizeModality($p);
            if ($m) {
                $set[$m] = true;
            }
        }

        return array_keys($set);
    }

    protected function normalizeModality(?string $value): ?string
    {
        if (! $value) return null;

        $v = strtoupper(trim($value));

        if (str_starts_with($v, 'PRE') || str_starts_with($v, 'ONS')) return 'PRESENCIAL';
        if (str_starts_with($v, 'HIB') || str_starts_with($v, 'HÍB') || str_starts_with($v, 'HYB')) return 'HIBRIDA';
        if (str_starts_with($v, 'REM') || str_starts_with($v, 'TEL')) return 'REMOTA';

        return null;
    }

    /* ===================== UBICACIÓN ===================== */

    protected function scoreLocation(Student $student, Vacancy $vacancy): float
    {
        // Obtener valores normalizados
        $cityStudent = $this->normString($student->city ?? null);
        $cityVac     = $this->normString($vacancy->city ?? null);
        $provinceStudent = $this->normString($student->province ?? null);
        $provinceVac     = $this->normString($vacancy->province ?? null);
        $mode = $this->normalizeModality($vacancy->mode ?? null);

        // REGLA 1: Teletrabajo/Remoto siempre es compatible al 100%
        if ($mode === 'REMOTA') {
            return 1.0;
        }

        // Si falta información de ubicación del estudiante, no puede puntuar
        if (!$provinceStudent) {
            return 0.0;
        }

        // Si falta información de la vacante, no puede puntuar
        if (!$provinceVac) {
            return 0.0;
        }

        // REGLA 2: Coinciden provincia Y municipio = Compatible 100%
        if ($provinceStudent === $provinceVac && $cityStudent && $cityVac && $cityStudent === $cityVac) {
            return 1.0;
        }

        // REGLA 3: Solo coincide provincia + (Presencial o Híbrido) = La mitad
        if ($provinceStudent === $provinceVac && ($mode === 'PRESENCIAL' || $mode === 'HIBRIDA')) {
            return 0.5;
        }

        // REGLA 4: No coincide la provincia + (Presencial o Híbrido) = Incompatible
        return 0.0;
    }

    protected function normString(?string $value): ?string
    {
        if (! $value) return null;
        $value = trim(mb_strtolower($value));
        return $value !== '' ? $value : null;
    }

    /* ===================== TECNOLOGÍAS ===================== */

    protected function scoreTechnologies(Student $student, Vacancy $vacancy): float
    {
        $studentTechs = $this->normalizeList($student->tech_competencies ?? null);
        $vacTechs     = $this->normalizeList($vacancy->tech_stack ?? $vacancy->competencies ?? null);

        if (empty($vacTechs) || empty($studentTechs)) {
            return 0.0;
        }

        $matched = array_intersect($vacTechs, $studentTechs);

        // Cobertura de lo que pide la vacante:
        $required = count($vacTechs);

        if ($required === 0) {
            return 0.0;
        }

        return count($matched) / $required;
    }

    /**
     * Normaliza una lista (json, array, string con comas…) a array de slugs en minúsculas.
     */
    protected function normalizeList($value): array
    {
        if (! $value) {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                $value = explode(',', $value);
            }
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        $out = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $name = $item['name'] ?? $item['slug'] ?? $item['label'] ?? $item['value'] ?? null;
            } else {
                $name = (string) $item;
            }

            $name = trim(mb_strtolower($name));
            if ($name !== '') {
                $out[$name] = true;
            }
        }

        return array_keys($out);
    }

    /* ===================== IDIOMAS ===================== */

    protected function scoreLanguages(Student $student, Vacancy $vacancy): float
    {
        // 1) Alumno: preferir relación pivot (alumno_idioma)
        $studentLangs = [];
        try {
            $stuLangRows = $student->languages()->get(); // fuerza la relación pivot
        } catch (\Throwable $e) {
            $stuLangRows = collect();
        }

        if ($stuLangRows->isEmpty()) {
            // fallback al atributo JSON 'languages' (array de objetos sin id)
            $raw = $student->getAttribute('languages') ?? [];
            foreach ($raw as $item) {
                if (!is_array($item)) continue;
                $code = strtolower(trim($item['code'] ?? $item['idioma'] ?? $item['name'] ?? ''));
                $level = strtoupper(trim($item['level'] ?? $item['nivel'] ?? 'A1'));
                if ($code !== '') {
                    $studentLangs['code:' . $code] = $level;
                }
            }
        } else {
            foreach ($stuLangRows as $lang) {
                $id = $lang->id ?? null;
                $level = strtoupper($lang->pivot->nivel ?? $lang->pivot->level ?? 'A1');
                if ($id) {
                    $studentLangs[$id] = $level;
                }
            }
        }

        // 2) Vacante: preferir pivot requiredLanguages
        $vacLangs = [];
        try {
            $vacLangRows = $vacancy->requiredLanguages()->get();
        } catch (\Throwable $e) {
            $vacLangRows = collect();
        }

        if ($vacLangRows->isEmpty()) {
            // Si la vacante no especifica idiomas, no penaliza → compatible al 100%
            return 1.0;
        } else {
            foreach ($vacLangRows as $lang) {
                $id = $lang->id ?? null;
                $min = strtoupper($lang->pivot->min_level ?? $lang->pivot->nivel_minimo ?? 'A1');
                if ($id) {
                    $vacLangs[$id] = $min;
                }
            }
        }

        if (empty($studentLangs)) {
            return 0.0;
        }

        $required = count($vacLangs);
        $matched  = 0.0;

        foreach ($vacLangs as $key => $reqLevel) {
            if (str_starts_with((string)$key, 'code:')) {
                $code = substr($key, 5);
                $stuLevel = $studentLangs['code:' . $code] ?? null;
            } else {
                $stuLevel = $studentLangs[$key] ?? null;
            }

            if (! $stuLevel) continue;

            $cmp = $this->compareLangLevel($stuLevel, $reqLevel);

            if ($cmp >= 0) {
                $matched += 1.0;
            } elseif ($cmp === -1) {
                $matched += 0.3;
            }
        }

        return $required ? max(0.0, min(1.0, $matched / $required)) : 1.0;
    }

    /**
     * Devuelve ['en' => 'B1', 'es' => 'C1', ...] a partir de json/array/string.
     */
    protected function normalizeLanguageList($value): array
    {
        if (! $value) {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                // Formato "EN:B1,ES:C1"
                $items = explode(',', $value);
                $out = [];
                foreach ($items as $item) {
                    $parts = explode(':', $item);
                    if (count($parts) === 2) {
                        $code  = strtolower(trim($parts[0]));
                        $level = strtoupper(trim($parts[1]));
                        if ($code) {
                            $out[$code] = $level ?: 'A1';
                        }
                    }
                }
                return $out;
            }
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        $out = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $code  = strtolower(trim($item['code'] ?? $item['slug'] ?? $item['idioma'] ?? ''));
                $level = strtoupper(trim($item['level'] ?? $item['nivel'] ?? ''));
            } else {
                // Fallback: "EN B1"
                $parts = preg_split('/\s+/', (string) $item);
                $code  = strtolower($parts[0] ?? '');
                $level = strtoupper($parts[1] ?? '');
            }

            if ($code === '') {
                continue;
            }
            if ($level === '') {
                $level = 'A1';
            }

            $out[$code] = $level;
        }

        return $out;
    }

    /**
     * Compara niveles tipo A1 < A2 < B1 < B2 < C1 < C2.
     * Devuelve:
     *  1  si alumno >= requerido
     *  0  si igual
     * -1  si alumno < requerido
     */
    protected function compareLangLevel(string $student, string $required): int
    {
        $order = ['A1' => 1, 'A2' => 2, 'B1' => 3, 'B2' => 4, 'C1' => 5, 'C2' => 6];

        $s = $order[$student]  ?? 0;
        $r = $order[$required] ?? 0;

        if ($s === $r) return 0;

        return $s > $r ? 1 : -1;
    }

    protected function scoreFpType(Student $student, Vacancy $vacancy): array
    {
        // Suponiendo que el estudiante tiene un campo fp_modality: 'general' o 'dual'
        $studentFp = strtolower($student->fp_modality ?? '');
        $acceptsGeneral = (bool)($vacancy->accepts_fp_general ?? false);
        $acceptsDual = (bool)($vacancy->accepts_fp_dual ?? false);

        if ($studentFp === 'general' && $acceptsGeneral) {
            return [1.0, true];
        }
        if ($studentFp === 'dual' && $acceptsDual) {
            return [1.0, true];
        }
        return [0.0, false];
    }
}
