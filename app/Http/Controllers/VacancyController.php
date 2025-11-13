<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VacancyController extends Controller
{
    // DASHBOARD mezcla; lo mantenemos tal cual lo tuvieras.
    public function dashboard(Request $request)
    {
        return Inertia::render('Dashboard', [
            'role' => $request->user()?->role,
        ]);
    }

    // LISTADO PÚBLICO PARA ALUMNOS — ordenado por compatibilidad y filtrado >= 50
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || ($user->role !== 'student' && !$user->student)) {
            abort(403, 'Solo alumnos');
        }
        $studentId = $user->student?->id;
        if (!$studentId) {
            return Inertia::render('Vacancies/Index', ['items' => []]);
        }

        // Cargamos todas publicadas para calcular score en PHP
        $vacancies = Vacancy::query()
            ->with(['company:id,trade_name,legal_name,city,province'])
            ->where('status', 'published')
            ->get();

        // Datos alumno (competencias e idiomas)
        $studentCompetencies = collect(DB::table('alumno_competencia')->where('student_id', $studentId)->pluck('competency_id'))->unique()->values();
        $studentLangs = DB::table('alumno_idioma')->where('student_id', $studentId)->get()
            ->mapWithKeys(function ($row) {
                return [$row->language_id => strtoupper($row->level ?? '')];
            });

        $student = DB::table('students')->where('id', $studentId)->first();

        $scored = $vacancies->map(function (Vacancy $v) use ($student, $studentCompetencies, $studentLangs) {
            $score = $this->computeScore($student, $studentCompetencies, $studentLangs, $v);
            return [
                'id' => $v->id,
                'title' => $v->title,
                'city' => $v->city,
                'province' => $v->province,
                'mode' => $v->mode,
                'cycle_required' => $v->cycle_required,
                'paid' => $v->paid,
                'salary_month' => $v->salary_month,
                'company' => [
                    'trade_name' => $v->company?->trade_name,
                    'legal_name' => $v->company?->legal_name,
                ],
                'score' => (int) round($score),
            ];
        })->filter(fn($i) => $i['score'] >= 50)
          ->sortByDesc('score')
          ->values();

        return Inertia::render('Vacancies/Index', [
            'items' => $scored,
        ]);
    }

    // FORM crear — solo empresa
    public function create(Request $request)
    {
        $user = $request->user();
        if (!$user || ($user->role !== 'company' && !$user->company)) {
            abort(403, 'Solo empresas');
        }

        return Inertia::render('Vacancies/Create', [
            'languages' => Language::orderBy('name')->get(['id','name']),
        ]);
    }

    // STORE crear — guarda vacante + idiomas requeridos
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || ($user->role !== 'company' && !$user->company)) {
            abort(403, 'Solo empresas');
        }
        $companyId = $user->company()->value('id');

        $data = $request->validate([
            'title'           => ['required','string','max:255'],
            'description'     => ['nullable','string'],
            'cycle_required'  => ['nullable','string','max:50'],
            'mode'            => ['nullable','in:onsite,remote,hybrid'],
            'city'            => ['nullable','string','max:255'],
            'province'        => ['nullable','string','max:255'],
            'hours_per_week'  => ['nullable','integer','min:1','max:40'],
            'duration_weeks'  => ['nullable','integer','min:1','max:52'],
            'paid'            => ['boolean'],
            'salary_month'    => ['nullable','integer','min:0'],
            'status'          => ['required','in:draft,published'],
            'tech_stack'      => ['nullable','array'],
            'tech_stack.*'    => ['string','max:50'],
            'soft_skills'     => ['nullable','array'],
            'soft_skills.*'   => ['string','max:50'],
            // idiomas requeridos: array de objetos {language_id, min_level}
            'languages_required'               => ['nullable','array'],
            'languages_required.*.language_id' => ['required_with:languages_required','integer','exists:languages,id'],
            'languages_required.*.min_level'   => ['nullable','in:A1,A2,B1,B2,C1,C2'],
        ]);

        $data['company_id'] = $companyId;
        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $vacancy = Vacancy::create($data);

        // Sincroniza idiomas requeridos
        $pivot = [];
        foreach ($data['languages_required'] ?? [] as $row) {
            $pivot[$row['language_id']] = [
                'min_level' => $row['min_level'] ?? null,
                'required'  => true,
            ];
        }
        if (!empty($pivot)) {
            $vacancy->requiredLanguages()->sync($pivot);
        }

        return redirect()->route('vacancies.my')->with('success', 'Vacante creada.');
    }

    // MIS VACANTES (empresa)
    public function myIndex(Request $request)
    {
        $companyId = $request->user()->company()->value('id');

        $vacancies = Vacancy::query()
            ->where('company_id', $companyId)
            ->latest('id')
            ->get(['id','title','status','published_at','city','province','mode','paid','salary_month']);

        return Inertia::render('Vacancies/MyIndex', [
            'items' => $vacancies,
        ]);
    }

    public function show(Request $request, Vacancy $vacancy)
    {
        $vacancy->load(['company:id,trade_name,legal_name,city,province','requiredLanguages:id,name']);
        return Inertia::render('Vacancies/Show', [
            'vacancy' => $vacancy,
            'canMatch' => $request->user()?->role === 'student',
        ]);
    }

    // --------- SCORING ----------
    private function computeScore($student, $studentCompetencies, $studentLangs, Vacancy $v): float
    {
        $score = 0.0;

        // 1) Tech (40) — Jaccard competencias alumno vs requeridas
        $reqComp = collect(DB::table('vacante_competencia_req')->where('vacancy_id', $v->id)->pluck('competency_id'));
        $inter = $reqComp->intersect($studentCompetencies)->count();
        $union = $reqComp->merge($studentCompetencies)->unique()->count();
        $tech = ($union > 0) ? ($inter / $union) : 0;
        $score += 40 * $tech;

        // 2) Ciclo (15)
        $cycleScore = 0;
        $alCycle = strtoupper(trim((string)($student->cycle ?? '')));
        $vaCycle = strtoupper(trim((string)($v->cycle_required ?? '')));
        if ($vaCycle && $alCycle) {
            if ($alCycle === $vaCycle) $cycleScore = 1.0;
            elseif (str_contains($alCycle, 'DA') && str_contains($vaCycle, 'DA')) $cycleScore = 0.55; // familia afín DAM/DAW
        }
        $score += 15 * $cycleScore;

        // 3) Ubicación+modalidad (15 => 8 + 7)
        $loc = 0;
        $stuCity = strtoupper((string)($student->city ?? ''));
        $stuProv = strtoupper((string)($student->province ?? ''));
        $vaCity  = strtoupper((string)($v->city ?? ''));
        $vaProv  = strtoupper((string)($v->province ?? ''));
        if ($stuCity && $vaCity && $stuCity === $vaCity)       $loc = 1.0;
        elseif ($stuProv && $vaProv && $stuProv === $vaProv)   $loc = 0.65;
        $score += 8 * $loc;

        $modeScore = 0;
        $mode = $v->mode;
        if ($mode === 'remote')  $modeScore = 1.0;
        elseif ($mode === 'hybrid') $modeScore = 0.6;
        elseif ($mode === 'onsite') $modeScore = 0.5;
        $score += 7 * $modeScore;

        // 4) Idiomas (10) — cumple mínimos
        $langRows = DB::table('vacante_idioma_req')->where('vacancy_id', $v->id)->get();
        $map = ['A1'=>1,'A2'=>2,'B1'=>3,'B2'=>4,'C1'=>5,'C2'=>6];
        $langScore = 0;
        $need = max(1, $langRows->count());
        foreach ($langRows as $r) {
            $min = $map[strtoupper($r->min_level ?? '')] ?? 0;
            $stu = $map[$studentLangs[$r->language_id] ?? ''] ?? 0;
            if ($stu === 0) { /* no suma */ }
            elseif ($min === 0) { $langScore += 1; } // sin mínimo, cuenta como ok
            else {
                if ($stu >= $min)      $langScore += 1.0;
                elseif ($stu + 1 === $min) $langScore += 0.6;
                else                         $langScore += 0.2;
            }
        }
        $score += 10 * ($langScore / $need);

        // 5) Soft skills (7) — si las usas como arrays JSON
        $soft = 0;
        $stuSoft = collect(json_decode($student->soft_skills ?? '[]', true));
        $vacSoft = collect($v->soft_skills ?? []);
        $u = $stuSoft->merge($vacSoft)->unique()->count();
        $i = $stuSoft->intersect($vacSoft)->count();
        if ($u > 0) $soft = $i/$u;
        $score += 7 * $soft;

        // 6) Experiencia/educación extra (3)
        $hasExp = DB::table('student_experiences')->where('student_id', $student->id)->exists();
        $hasEdu = DB::table('student_educations')->where('student_id', $student->id)->exists();
        if ($hasExp || $hasEdu) $score += 3;

        return min(100, $score);
    }
}
