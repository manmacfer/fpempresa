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
        $query = Vacancy::query()
            ->with('company')
            ->where('status', 'published');

        // Filtro por ciclo (texto libre: "DAM", "DAW", "1 DAW"...)
        if (!empty($filters['ciclo'])) {
            $value = trim($filters['ciclo']);
            $query->where(function ($q) use ($value) {
                $q->where('cycle_required', 'LIKE', "%{$value}%")
                  ->orWhereNull('cycle_required');
            });
        }

        // Filtro por modalidad (presencial / híbrido / remoto)
        if (!empty($filters['modalidad'])) {
            $mod = strtoupper($filters['modalidad']);
            $query->where('modalidad', $mod);
        }

        // Filtro por ubicación (ciudad o provincia contiene el texto)
        if (!empty($filters['ubicacion'])) {
            $ubi = trim($filters['ubicacion']);
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
                return $row['eligible'] && $row['score'] >= $minScore;
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
        $studentCycle = $this->parseCycle($student->cycle);
        $vacCycle     = $this->parseCycle($vacancy->cycle_required);

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
        $studentFp = strtolower((string) $student->fp_modality);
        $acceptsGeneral = (bool) $vacancy->accepts_fp_general;
        $acceptsDual    = (bool) $vacancy->accepts_fp_dual;

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
        $studentSlot = $this->normalizeSlot($student->availability_slot);
        $vacSlots    = $this->normalizeVacancySlots($vacancy->horarios_disponibles);

        if (! $studentSlot || empty($vacSlots)) {
            return 0.0;
        }

        if ($studentSlot === 'AMBAS') {
            // Alumno flexible → si la vacante tiene mañana o tarde, cuenta completo
            return 1.0;
        }

        return in_array($studentSlot, $vacSlots, true) ? 1.0 : 0.0;
    }

    protected function normalizeSlot(?string $slot): ?string
    {
        if (! $slot) return null;

        $v = mb_strtolower(trim($slot));

        if (str_contains($v, 'mañana') || str_starts_with($v, 'm')) {
            return 'MANANA';
        }
        if (str_contains($v, 'tarde') || str_starts_with($v, 't')) {
            return 'TARDE';
        }
        if (str_contains($v, 'amb')) {
            return 'AMBAS';
        }

        return null;
    }

    protected function normalizeVacancySlots($value): array
    {
        $list = $this->normalizeList($value);
        $out  = [];

        foreach ($list as $item) {
            $slot = $this->normalizeSlot($item);
            if ($slot) {
                $out[$slot] = true;
            }
        }

        // Por si la vacante guarda un string simple en vez de json
        if (empty($out) && is_string($value)) {
            $slot = $this->normalizeSlot($value);
            if ($slot) {
                $out[$slot] = true;
            }
        }

        return array_keys($out);
    }

    /* ===================== MODALIDAD (multi alumno) ===================== */

    protected function scoreModalities(Student $student, Vacancy $vacancy): float
    {
        $studentMods = $this->studentModalities($student);
        $vacMod      = $this->normalizeModality($vacancy->modalidad ?? $vacancy->mode);

        if (! $vacMod || empty($studentMods)) {
            return 0.0;
        }

        return in_array($vacMod, $studentMods, true) ? 1.0 : 0.0;
    }

    protected function studentModalities(Student $student): array
    {
        $raw = $student->work_modalities ?? $student->work_modality;

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

        if (str_starts_with($v, 'PRE')) return 'PRESENCIAL';
        if (str_starts_with($v, 'HIB') || str_starts_with($v, 'HÍB')) return 'HIBRIDA';
        if (str_starts_with($v, 'REM') || str_starts_with($v, 'TEL')) return 'REMOTA';

        return null;
    }

    /* ===================== UBICACIÓN ===================== */

    protected function scoreLocation(Student $student, Vacancy $vacancy): float
    {
        // Ahora mismo en students solo tienes city (no provincia del alumno),
        // así que uso: 1.0 si coincide city, 0.0 si no.
        $cityStudent = $this->normString($student->city);
        $cityVac     = $this->normString($vacancy->city);

        if (! $cityStudent || ! $cityVac) {
            return 0.0;
        }

        return $cityStudent === $cityVac ? 1.0 : 0.0;

        /*
         * Si más adelante añades provincia/municipio al alumno, aquí
         * podemos hacer:
         * - 1.0 si ciudad + provincia coinciden
         * - 0.5 si solo coincide ciudad
         * - 0.0 si nada coincide
         */
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
        $studentTechs = $this->normalizeList($student->tech_competencies);
        $vacTechs     = $this->normalizeList($vacancy->tech_stack ?? $vacancy->competencies);

        if (empty($vacTechs) || empty($studentTechs)) {
            return 0.0;
        }

        $matched = array_intersect($vacTechs, $studentTechs);

        // Cobertura de lo que pide la vacante:
        // si pide [JavaScript, PHP] y el alumno tiene ambos, 2/2 = 1.0 (máximo)
        // si solo tiene uno, 1/2 = 0.5, etc.
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
        $studentLangs = $this->normalizeLanguageList($student->languages);
        $vacLangs     = $this->normalizeLanguageList($vacancy->idiomas);

        if (empty($vacLangs) || empty($studentLangs)) {
            return 0.0;
        }

        $required = count($vacLangs);
        $matched  = 0.0;

        foreach ($vacLangs as $code => $reqLevel) {
            $stuLevel = $studentLangs[$code] ?? null;
            if (! $stuLevel) {
                continue;
            }

            $cmp = $this->compareLangLevel($stuLevel, $reqLevel);

            if ($cmp >= 0) {
                // mismo nivel o superior → 1 punto
                $matched += 1.0;
            } elseif ($cmp === -1) {
                // un poquito por debajo → 0.3 en vez de 0
                $matched += 0.3;
            }
        }

        if ($required === 0) {
            return 0.0;
        }

        $ratio = $matched / $required;

        return max(0.0, min(1.0, $ratio));
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
}
