<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VacancyController extends Controller
{
    // Tu dashboard actual (ajústalo si ya tenías lógica distinta)
    public function dashboard(Request $request)
    {
        return Inertia::render('Dashboard', [
            'role' => $request->user()?->role ?? null,
        ]);
    }

    // Listado general (para alumnos ver publicadas, por ejemplo)
    public function index(Request $request)
    {
        $q = Vacancy::query()
            ->with('company:id,trade_name,legal_name,city,province')
            ->where('status', 'published')
            ->latest('published_at');

        // pequeños filtros rápidos (opcionales)
        if ($cycle = $request->input('cycle')) {
            $q->where('cycle_required', $cycle);
        }
        if ($mode = $request->input('mode')) {
            $q->where('mode', $mode);
        }
        if ($city = $request->input('city')) {
            $q->where('city', 'like', '%'.$city.'%');
        }

        $vacancies = $q->paginate(12)->withQueryString();

        return Inertia::render('Vacancies/Index', [
            'vacancies' => $vacancies,
            'filters'   => [
                'cycle' => $cycle ?? null,
                'mode'  => $mode ?? null,
                'city'  => $city ?? null,
            ],
        ]);
    }

    // Solo empresa: formulario de creación
    public function create(Request $request)
    {
        // asumimos middleware role:company en la ruta
        return Inertia::render('Vacancies/Create', [
            'defaults' => [
                'mode' => 'onsite',
                'paid' => false,
                'status' => 'draft',
            ],
        ]);
    }

    // Solo empresa: guardar
    public function store(Request $request)
    {
        $user = $request->user();
        $company = $user->company()->firstOrFail();

        $data = $request->validate([
            'title'          => ['required','string','max:180'],
            'description'    => ['nullable','string'],
            'cycle_required' => ['nullable','string','max:50'],
            'mode'           => ['nullable','in:onsite,remote,hybrid'],
            'city'           => ['nullable','string','max:100'],
            'province'       => ['nullable','string','max:100'],
            'hours_per_week' => ['nullable','integer','min:1','max:40'],
            'duration_weeks' => ['nullable','integer','min:1','max:52'],
            'paid'           => ['boolean'],
            'salary_month'   => ['nullable','integer','min:0','max:100000'],
            'tech_stack'     => ['nullable','array'],
            'tech_stack.*'   => ['string','max:50'],
            'soft_skills'    => ['nullable','array'],
            'soft_skills.*'  => ['string','max:50'],
            'status'         => ['nullable','in:draft,published,closed'],
        ]);

        $data['company_id'] = $company->id;

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $vacancy = Vacancy::create($data);

        return redirect()->route('vacancies.my')->with('success', 'Vacante creada.');
    }

    // Solo empresa: mis vacantes
    public function myIndex(Request $request)
    {
        $companyId = $request->user()->company()->value('id');

        $vacancies = Vacancy::query()
            ->where('company_id', $companyId)
            ->latest('created_at')
            ->paginate(12);

        return Inertia::render('Vacancies/MyIndex', [
            'vacancies' => $vacancies,
        ]);
    }

    // Ver una vacante (alumno o empresa)
    public function show(Vacancy $vacancy)
    {
        $vacancy->load('company:id,trade_name,legal_name,city,province,website');

        return Inertia::render('Vacancies/Show', [
            'vacancy' => $vacancy,
        ]);
    }
}
