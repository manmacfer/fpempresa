<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Vacancy;
use Illuminate\Support\Str;

class CompatibilityService
{
    // pesos base: se renormalizan si falta algún criterio
    private array $weights = [
        'languages' => 0.40,
        'tech'      => 0.40,
        'cycle'     => 0.10,
        'mode'      => 0.05,
        'location'  => 0.05,
    ];

    private array $cefr = ['A1'=>1,'A2'=>2,'B1'=>3,'B2'=>4,'C1'=>5,'C2'=>6];

    public function score(Student $student, Vacancy $vacancy): array
    {
        $parts = [
            'languages' => $this->scoreLanguages($student, $vacancy), // null si vacante no pide idiomas
            'tech'      => $this->scoreTech($student, $vacancy),      // null si falta info
            'cycle'     => $this->scoreCycle($student, $vacancy),
            'mode'      => $this->scoreMode($student, $vacancy),
            'location'  => $this->scoreLocation($student, $vacancy),
        ];

        // renormaliza pesos descartando criterios null
        $used = array_filter($parts, fn($v) => $v !== null);
        $sumW = 0.0; $scaled = [];
        foreach ($parts as $k => $v) {
            if ($v === null) continue;
            $sumW += $this->weights[$k];
        }
        foreach ($parts as $k => $v) {
            if ($v === null) continue;
            $scaled[$k] = $v * ($this->weights[$k] / $sumW);
        }

        $score = array_sum($scaled); // 0..1
        return [
            'score'     => $score,
            'breakdown' => $parts,
        ];
    }

    private function scoreLanguages(Student $s, Vacancy $v): ?float
    {
        $req = $v->requiredLanguages ?? collect(); // belongsToMany->withPivot('min_level')
        if (!$req->count()) return null;

        // mapa lenguaje_id => nivel alumno (A1..C2)
        $stuMap = [];
        foreach (($s->languages ?? []) as $L) {
            $lvl = $L->pivot->level ?? $L->pivot->nivel ?? null;
            if (!$lvl) continue;
            $stuMap[$L->id] = $this->cefr[$lvl] ?? 0;
        }

        $ok = 0;
        foreach ($req as $R) {
            $need = $this->cefr[$R->pivot->min_level ?? 'A1'] ?? 1;
            $have = $stuMap[$R->id] ?? 0;
            if ($have >= $need) $ok++;
        }
        return $ok / max(1, $req->count());
    }

    private function scoreTech(Student $s, Vacancy $v): ?float
    {
        $vacTech = collect($v->tech_stack ?? [])->map(fn($t)=>$this->normTech($t))->filter()->unique()->values();
        if (!$vacTech->count()) return null;

        // del alumno sacamos competencias por nombre (o código) y las normalizamos
        $stuTech = collect($s->competencies ?? [])->map(function($c){
            $name = $c->name ?? $c->nombre ?? null;
            return $this->normTech($name);
        })->filter()->unique()->values();

        if (!$stuTech->count()) return 0.0;

        $overlap = $vacTech->intersect($stuTech)->count();
        return $overlap / $vacTech->count();
    }

    private function normTech(?string $t): ?string
    {
        if (!$t) return null;
        $t = Str::of($t)->lower()->trim();
        // equivalencias rápidas
        $map = [
            'php' => 'php', 'laravel'=>'laravel', 'symfony'=>'symfony',
            'java'=>'java', 'spring boot'=>'spring boot', 'spring'=>'spring boot',
            '.net'=>'.net', 'c#'=>'c#',
            'node'=>'node.js','nodejs'=>'node.js','node.js'=>'node.js','express'=>'express',
            'python'=>'python','django'=>'django','flask'=>'flask',
            'ruby on rails'=>'rails','rails'=>'rails',
            'go'=>'go','golang'=>'go','rust'=>'rust',
            'javascript'=>'javascript','js'=>'javascript',
            'typescript'=>'typescript','ts'=>'typescript',
            'vue'=>'vue','react'=>'react','angular'=>'angular','svelte'=>'svelte',
            'next'=>'next.js','next.js'=>'next.js','nuxt'=>'nuxt',
            'mysql'=>'mysql','mariadb'=>'mariadb','postgresql'=>'postgresql','postgres'=>'postgresql',
            'mongodb'=>'mongodb','redis'=>'redis','elasticsearch'=>'elasticsearch',
            'docker'=>'docker','kubernetes'=>'kubernetes','nginx'=>'nginx','apache'=>'apache',
            'github actions'=>'github actions','gitlab ci'=>'gitlab ci','ci/cd'=>'ci/cd',
            'aws'=>'aws','azure'=>'azure','gcp'=>'gcp','terraform'=>'terraform','ansible'=>'ansible',
            'rest'=>'rest','graphql'=>'graphql','grpc'=>'grpc','rabbitmq'=>'rabbitmq','kafka'=>'kafka',
            'html'=>'html','css'=>'css','sass'=>'sass','less'=>'less','tailwind'=>'tailwind',
            'vite'=>'vite','webpack'=>'webpack',
            'phpunit'=>'phpunit','pest'=>'pest','jest'=>'jest','vitest'=>'vitest','cypress'=>'cypress','playwright'=>'playwright',
        ];
        $key = (string)$t;
        return $map[$key] ?? (string)$t;
    }

    private function scoreCycle(Student $s, Vacancy $v): ?float
    {
        $stu = strtolower(trim($s->cycle ?? $s->ciclo ?? ''));
        $req = strtolower(trim($v->cycle_required ?? ''));
        if ($req === '') return null;
        if ($stu === '')  return 0.0;
        return $stu === $req ? 1.0 : (str_contains($stu, substr($req, -3)) ? 0.6 : 0.0);
    }

    private function scoreMode(Student $s, Vacancy $v): ?float
    {
        $mode = $v->mode ?? null; if (!$mode) return null;
        // si el alumno no tiene preferencia guardada, damos un leve beneficio a remoto/híbrido
        $pref = strtolower(trim($s->preferred_mode ?? $s->modalidad_preferida ?? ''));
        if ($pref === '') return $mode === 'remote' ? 1.0 : ($mode === 'hybrid' ? 0.7 : 0.5);
        return strtolower($mode) === $pref ? 1.0 : 0.4;
    }

    private function scoreLocation(Student $s, Vacancy $v): ?float
    {
        $mode = strtolower($v->mode ?? '');
        if ($mode === 'remote') return 1.0;

        $sc = strtolower(trim($s->city ?? $s->localidad ?? ''));
        $sp = strtolower(trim($s->province ?? $s->provincia ?? ''));
        $vc = strtolower(trim($v->city ?? ''));
        $vp = strtolower(trim($v->province ?? ''));

        if ($vc==='' && $vp==='') return null;
        if ($sc && $vc && $sc === $vc) return 1.0;
        if ($sp && $vp && $sp === $vp) return 0.8;
        return 0.3; // algo de afinidad aunque no coincida
    }
}
