<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VacancyController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Dashboard', ['role' => auth()->user()->role]);
    }

    public function index(Request $r)
    {
        $q = Vacancy::query()->latest();
        if ($r->filled('ciclo')) $q->where('ciclo_requerido', $r->ciclo);
        if ($r->filled('modalidad')) $q->where('modalidad', $r->modalidad);
        if ($r->filled('ubicacion')) $q->where('ubicacion', 'like', '%' . $r->ubicacion . '%');
        return Inertia::render('Vacancies/Index', [
            'filters' => $r->only('ciclo', 'modalidad', 'ubicacion'),
            'vacantes' => $q->paginate(10),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->isEmpresa() || auth()->user()->isAdmin(), 403);
        return Inertia::render('Vacancies/Create');
    }

    public function store(Request $r)
    {
        abort_unless(auth()->user()->isEmpresa() || auth()->user()->isAdmin(), 403);
        $data = $r->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'ciclo_requerido' => 'required|string|max:20',
            'modalidad' => 'required|string|in:presencial,híbrido,remoto',
            'ubicacion' => 'nullable|string|max:120',
        ]);
        $company = Company::firstOrCreate(['user_id' => auth()->id()], ['name' => auth()->user()->name]);
        Vacancy::create($data + ['company_id' => $company->id, 'status' => 'abierta']);
        return redirect()->route('vacancies.index')->with('success', 'Vacante creada.');
    }
}
