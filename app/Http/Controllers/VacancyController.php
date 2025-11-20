<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use App\Services\CompatibilityService;

class VacancyController extends Controller
{
    // DASHBOARD — renderiza la vista Dashboard (mantener comportamiento anterior)
    public function dashboard(Request $request)
    {
        return Inertia::render('Dashboard', [
            'role' => $request->user()?->role,
        ]);
    }

    // LISTADO PÚBLICO PARA ALUMNOS — ordenado por compatibilidad (sin umbral 50)
    public function index(Request $request, CompatibilityService $svc)
    {
        $user = $request->user();

        // Si es empresa, redirige a "mis vacantes"
        if ($user?->role === 'company' || ($user && $user->company()->exists() && !$user->student()->exists())) {
            return redirect()->route('vacancies.my');
        }

        // Alumno: calculamos compatibilidad sobre publicadas
        $student = $user?->student;
        $vacancies = Vacancy::query()
            ->where('status', 'published')
            ->with(['company', 'requiredLanguages'])
            ->latest('published_at')
            ->get();

        $items = [];
        foreach ($vacancies as $v) {
            $calc = $student ? $svc->scoreStudentForVacancy($student, $v) : ['score' => 0];
            $pct  = (int) round($calc['score'] ?? 0); // score ya está en 0..100

            $items[] = [
                'id'       => $v->id,
                'title'    => $v->title,
                'company'  => [
                    'trade_name' => $v->company->trade_name ?? null,
                    'legal_name' => $v->company->legal_name ?? null,
                ],
                'city'     => $v->city,
                'province' => $v->province,
                'mode'     => $v->mode,
                'score'    => $pct,
            ];
        }

        // ordenar por score desc y luego fecha
        usort($items, function ($a, $b) use ($vacancies) {
            if ($b['score'] === $a['score']) {
                $ad = $vacancies->firstWhere('id', $a['id'])->published_at ?? null;
                $bd = $vacancies->firstWhere('id', $b['id'])->published_at ?? null;
                return strcmp((string)$bd, (string)$ad);
            }
            return $b['score'] <=> $a['score'];
        });

        return Inertia::render('Vacancies/CompatIndex', [
            'items' => $items,
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
            'languages' => Language::orderBy('name')->get(['id', 'name']),
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
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'cycle_required'  => ['nullable', 'string', 'max:50'],
            'mode'            => ['nullable', 'in:presencial,remoto,hibrido'],
            'city'            => ['nullable', 'string', 'max:255'],
            'province'        => ['nullable', 'string', 'max:255'],
            'hours_per_week'  => ['nullable', 'integer', 'min:1', 'max:40'],
            'duration_weeks'  => ['nullable', 'integer', 'min:1', 'max:52'],
            'paid'            => ['boolean'],
            'salary_month'    => ['nullable', 'integer', 'min:0'],
            'status'          => ['required', 'in:draft,published'],
            'tech_stack'      => ['nullable', 'array'],
            'tech_stack.*'    => ['string', 'max:50'],
            'soft_skills'     => ['nullable', 'array'],
            'soft_skills.*'   => ['string', 'max:50'],
            'languages_required'               => ['nullable', 'array'],
            'languages_required.*.language_id' => ['required_with:languages_required', 'integer', 'exists:languages,id'],
            'languages_required.*.min_level'   => ['nullable', 'in:A1,A2,B1,B2,C1,C2'],
        ]);

        $data['company_id'] = $companyId;
        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $vacancy = Vacancy::create($data);

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
            ->get(['id', 'title', 'status', 'published_at', 'city', 'province', 'mode', 'paid', 'salary_month']);

        return Inertia::render('Vacancies/MyIndex', [
            'items' => $vacancies,
        ]);
    }

public function show(Request $request, Vacancy $vacancy)
{
    $user = $request->user();
    
    // Obtener el rol: puede venir como string directo o desde role_id
    $role = $user?->role ?? null;
    if (!$role && $user?->role_id) {
        // Fallback: mapeo de role_id a string (por compatibilidad)
        $roleId = $user->role_id;
        if ($roleId == 3) {
            $role = 'student';
        } elseif ($roleId == 4) {
            $role = 'company';
        }
    }

    // Verificar si el estudiante ya tiene una candidatura para esta vacante
    $hasApplication = false;
    $applicationId = null;
    if ($user && $role === 'student' && $user->student) {
        $application = \App\Models\Application::where('student_id', $user->student->id)
            ->where('vacancy_id', $vacancy->id)
            ->first();
        
        if ($application) {
            $hasApplication = true;
            $applicationId = $application->id;
        }
    }

    $canMatch = $user && (
        $role === 'student'
        && $vacancy->status === 'published'
        && !$hasApplication  // Solo puede dar match si no tiene candidatura
    );

    return inertia('Vacancies/Show', [
        'vacancy' => $vacancy->load('company', 'requiredLanguages'),
        'canMatch' => $canMatch,
        'hasApplication' => $hasApplication,
        'applicationId' => $applicationId,
        'auth' => [
            'id' => $user?->id,
            'name' => $user?->name,
            'role' => $role,      // <-- aquí solo el string 'student' o 'company'
            'roleId' => $user?->role_id,  // <-- aquí el número
        ],
    ]);
}

    // Editar (formulario) — solo propietario empresa.
    public function edit(Request $request, Vacancy $vacancy)
    {
        $user = $request->user();
        $company = $user->company ?? null;
        if (! $company || $company->id !== $vacancy->company_id) {
            abort(403);
        }

        $vacancy->load('requiredLanguages');
        $languages = Language::select('id', 'name')->get();

        return Inertia::render('Vacancies/Edit', [
            'vacancy' => $vacancy,
            'languages' => $languages,
        ]);
    }

    // Actualizar vacante.
    public function update(Request $request, Vacancy $vacancy): RedirectResponse
    {
        $user = $request->user();
        $company = $user->company ?? null;
        if (! $company || $company->id !== $vacancy->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cycle_required' => 'nullable|string',
            'mode' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'hours_per_week' => 'nullable|integer',
            'duration_weeks' => 'nullable|integer',
            'paid' => 'sometimes|boolean',
            'salary_month' => 'nullable|numeric',
            'status' => 'nullable|string',
            'tech_stack' => 'nullable|array',
            'soft_skills' => 'nullable|array',
            'required_languages' => 'nullable|array',
            'accepts_fp_general' => 'boolean',
            'accepts_fp_dual' => 'boolean',
            'availability_slot' => ['required', 'in:manana,tarde,completa'],
        ]);

        $vacancy->update(array_merge($validated, [
            'paid' => !empty($validated['paid']),
            'availability_slot' => $validated['availability_slot'],
        ]));

        if (isset($validated['required_languages'])) {
            $sync = [];
            foreach ($validated['required_languages'] as $lang) {
                if (isset($lang['id'])) {
                    $sync[$lang['id']] = [
                        'min_level' => $lang['min_level'] ?? null,
                    ];
                }
            }
            $vacancy->requiredLanguages()->sync($sync);
        }

        return redirect()->route('vacancies.show', $vacancy->id)->with('success', 'Vacante actualizada.');
    }

    // Borrar vacante.
    public function destroy(Request $request, Vacancy $vacancy): RedirectResponse
    {
        $user = $request->user();
        $company = $user->company ?? null;
        if (! $company || $company->id !== $vacancy->company_id) {
            abort(403);
        }

        $vacancy->delete();

        return redirect()->route('vacancies.my')->with('success', 'Vacante eliminada.');
    }

    // --------- SCORING (si lo necesitas interno) ----------
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
            elseif (str_contains($alCycle, 'DA') && str_contains($vaCycle, 'DA')) $cycleScore = 0.55;
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

        // 4) Idiomas (10)
        $langRows = DB::table('vacante_idioma_req')->where('vacancy_id', $v->id)->get();
        $map = ['A1' => 1, 'A2' => 2, 'B1' => 3, 'B2' => 4, 'C1' => 5, 'C2' => 6];
        $langScore = 0;
        $need = max(1, $langRows->count());
        foreach ($langRows as $r) {
            $min = $map[strtoupper($r->min_level ?? '')] ?? 0;
            $stu = $map[$studentLangs[$r->language_id] ?? ''] ?? 0;
            if ($stu === 0) {
            } elseif ($min === 0) {
                $langScore += 1;
            } else {
                if ($stu >= $min)      $langScore += 1.0;
                elseif ($stu + 1 === $min) $langScore += 0.6;
                else                         $langScore += 0.2;
            }
        }
        $score += 10 * ($langScore / $need);

        // 5) Soft skills (7)
        $soft = 0;
        $stuSoft = collect(json_decode($student->soft_skills ?? '[]', true));
        $vacSoft = collect($v->soft_skills ?? []);
        $u = $stuSoft->merge($vacSoft)->unique()->count();
        $i = $stuSoft->intersect($vacSoft)->count();
        if ($u > 0) $soft = $i / $u;
        $score += 7 * $soft;

        // 6) Experiencia/educación extra (3)
        $hasExp = DB::table('student_experiences')->where('student_id', $student->id)->exists();
        $hasEdu = DB::table('student_educations')->where('student_id', $student->id)->exists();
        if ($hasExp || $hasEdu) $score += 3;

        return min(100, $score);
    }
}
