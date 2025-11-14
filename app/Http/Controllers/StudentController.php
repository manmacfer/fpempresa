<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Competency;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function editMe(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => $request->user()->id]);

        // Cargamos TODO lo que afecta a la ficha y a la compatibilidad
        $student->load([
            'educations'  => fn ($q) => $q->orderBy('start_date', 'desc'),
            'experiences' => fn ($q) => $q->orderBy('start_date', 'desc'),
            'competencies',
            'languages',
        ]);

        // Catálogos: los MISMOS que usan las vacantes
        $competencies = Competency::query()
            ->orderBy('nombre')
            ->get(['id', 'slug', 'nombre as name']);

        $languages = Language::query()
            ->orderBy('nombre')
            ->get(['id', 'codigo as code', 'nombre as name']);

        return Inertia::render('Students/Edit', [
            'student'         => $this->toResource($student),
            'allCompetencies' => $competencies,
            'allLanguages'    => $languages,
        ]);
    }

    public function updateMe(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => $request->user()->id]);

        Log::info('students.update.me payload', $request->all());

        $data = $request->validate([
            // personales / contacto
            'dni'                 => ['nullable', 'string', 'max:50'],
            'phone'               => ['nullable', 'string', 'max:50'],
            'birth_date'          => ['nullable', 'date'],
            'address'             => ['nullable', 'string', 'max:255'],
            'postal_code'         => ['nullable', 'string', 'max:20'],
            'city'                => ['nullable', 'string', 'max:100'],   // municipio/población
            'province'            => ['nullable', 'string', 'max:100'],
            'has_driver_license'  => ['nullable', 'boolean'],
            'has_vehicle'         => ['nullable', 'boolean'],

            // académicos
            'cycle'               => ['nullable', 'string', 'max:20'],
            'center'              => ['nullable', 'string', 'max:255'],
            'year_start'          => ['nullable', 'integer'],
            'year_end'            => ['nullable', 'integer'],
            'fp_modality'         => ['nullable', 'in:dual,general'],

            // disponibilidad
            'availability_slot'   => ['nullable', 'in:morning,afternoon,both'],
            'commitments'         => ['nullable', 'array'],
            'commitments.*'       => ['string'],
            'relocate'            => ['nullable', 'boolean'],
            'relocate_cities'     => ['nullable', 'array'],
            'relocate_cities.*'   => ['string'],
            'transport_own'       => ['nullable', 'boolean'],
            'work_modality'       => ['nullable', 'in:presencial,remota,hibrida'],
            'remote_days'         => ['nullable', 'integer', 'min:0', 'max:7'],
            'days_per_week'       => ['nullable', 'integer', 'min:1', 'max:7'],
            'available_from'      => ['nullable', 'date'],

            // intereses / perfil (texto libre)
            'sectors'                 => ['nullable', 'array'],
            'sectors.*'               => ['string'],
            'preferred_company_type'  => ['nullable', 'string', 'max:255'],
            'non_formal_experience'   => ['nullable', 'string', 'max:255'],
            'tech_competencies'       => ['nullable', 'array'],
            'tech_competencies.*'     => ['string'],
            'soft_skills'             => ['nullable', 'array'],
            'soft_skills.*'           => ['string'],

            // COMPATIBILIDAD: competencias técnicas (catálogo)
            'competency_ids'          => ['nullable', 'array'],
            'competency_ids.*'        => ['integer', 'exists:competencies,id'],

            // COMPATIBILIDAD: idiomas (catálogo + nivel)
            'languages'               => ['nullable', 'array'],
            'languages.*.language_id' => ['required', 'integer', 'exists:languages,id'],
            'languages.*.level'       => ['required', 'in:A1,A2,B1,B2,C1,C2'],

            // extra
            'hobbies'             => ['nullable', 'string', 'max:255'],
            'clubs'               => ['nullable', 'string', 'max:255'],
            'align_activities'    => ['nullable', 'boolean'],
            'entrepreneurship'    => ['nullable', 'string'],

            // links
            'link_linkedin'       => ['nullable', 'url'],
            'link_portfolio'      => ['nullable', 'url'],
            'link_github'         => ['nullable', 'url'],
            'link_video'          => ['nullable', 'url'],

            // ficheros
            'avatar'              => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'cv'                  => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
            'cover_letter'        => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
            'other_certs'         => ['nullable', 'array'],
            'other_certs.*'       => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
        ]);

        // Sacamos arrays de relaciones antes del fill
        $competencyIds   = $data['competency_ids'] ?? null;
        $languagePayload = $data['languages'] ?? null;
        unset($data['competency_ids'], $data['languages']);

        // Subida de ficheros (si vienen)
        if ($request->hasFile('avatar')) {
            if ($student->avatar_path) {
                Storage::disk('public')->delete($student->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }
        if ($request->hasFile('cv')) {
            if ($student->cv_path) {
                Storage::disk('public')->delete($student->cv_path);
            }
            $data['cv_path'] = $request->file('cv')->store('cv', 'public');
        }
        if ($request->hasFile('cover_letter')) {
            if ($student->cover_letter_path) {
                Storage::disk('public')->delete($student->cover_letter_path);
            }
            $data['cover_letter_path'] = $request->file('cover_letter')->store('cover_letters', 'public');
        }
        if ($request->hasFile('other_certs')) {
            $paths = [];
            foreach ($request->file('other_certs') as $file) {
                $paths[] = $file->store('certs', 'public');
            }
            $data['other_certs_paths'] = $paths;
        }

        // Guarda campos "simples"
        $student->fill($data);
        $student->saveOrFail();

        // Sincroniza competencias técnicas (catálogo)
        if ($competencyIds !== null) {
            $student->competencies()->sync($competencyIds);
        }

        // Sincroniza idiomas (pivot alumno_idioma)
        if ($languagePayload !== null) {
            $syncData = [];
            foreach ($languagePayload as $row) {
                $syncData[$row['language_id']] = ['nivel' => $row['level']];
            }
            $student->languages()->sync($syncData);
        }

        return redirect()->route('students.edit.me')->with('success', 'Perfil guardado.');
    }

    public function publicShow(Student $student)
    {
        $student->load([
            'educations'  => fn ($q) => $q->orderBy('start_date', 'desc'),
            'experiences' => fn ($q) => $q->orderBy('start_date', 'desc'),
            'competencies',
            'languages',
        ]);

        return Inertia::render('Students/PublicShow', [
            'student' => $this->toPublicResource($student),
        ]);
    }

        // ===== Resources =====
    protected function toResource(Student $s): array
    {
        // Forzamos a usar SIEMPRE las relaciones, no un posible atributo array
        $competenciesRel = $s->relationLoaded('competencies')
            ? $s->getRelation('competencies')
            : $s->competencies()->get();

        $languagesRel = $s->relationLoaded('languages')
            ? $s->getRelation('languages')
            : $s->languages()->get();

        return [
            'id'                  => $s->id,
            // personales
            'user_name'           => optional($s->user)->name,
            'dni'                 => $s->dni,
            'phone'               => $s->phone,
            'birth_date'          => optional($s->birth_date)?->format('Y-m-d'),
            'address'             => $s->address,
            'postal_code'         => $s->postal_code,
            'city'                => $s->city,    // municipio/población
            'province'            => $s->province,
            'has_driver_license'  => $s->has_driver_license,
            'has_vehicle'         => $s->has_vehicle,
            // académicos
            'cycle'               => $s->cycle,
            'center'              => $s->center,
            'year_start'          => $s->year_start,
            'year_end'            => $s->year_end,
            'fp_modality'         => $s->fp_modality,
            // disponibilidad
            'availability_slot'   => $s->availability_slot,
            'commitments'         => $s->commitments ?? [],
            'relocate'            => $s->relocate,
            'relocate_cities'     => $s->relocate_cities ?? [],
            'transport_own'       => $s->transport_own,
            'work_modality'       => $s->work_modality,
            'remote_days'         => $s->remote_days,
            'days_per_week'       => $s->days_per_week,
            'available_from'      => optional($s->available_from)?->format('Y-m-d'),
            // intereses (texto libre)
            'sectors'                 => $s->sectors ?? [],
            'preferred_company_type'  => $s->preferred_company_type,
            'non_formal_experience'   => $s->non_formal_experience,
            'tech_competencies'       => $s->tech_competencies ?? [],
            'soft_skills'             => $s->soft_skills ?? [],
            'certifications'          => $s->certifications ?? [],
            // extra
            'hobbies'             => $s->hobbies,
            'clubs'               => $s->clubs,
            'align_activities'    => $s->align_activities,
            'entrepreneurship'    => $s->entrepreneurship,
            // links
            'link_linkedin'       => $s->link_linkedin,
            'link_portfolio'      => $s->link_portfolio,
            'link_github'         => $s->link_github,
            'link_video'          => $s->link_video,
            // ficheros
            'avatar_url'          => $s->avatar_url,
            // relaciones para ficha
            'educations' => $s->educations->map(fn ($e) => [
                'id'         => $e->id,
                'title'      => $e->title,
                'center'     => $e->center,
                'start_date' => optional($e->start_date)?->format('Y-m-d'),
                'end_date'   => optional($e->end_date)?->format('Y-m-d'),
            ])->values(),
            'experiences' => $s->experiences->map(fn ($x) => [
                'id'           => $x->id,
                'company'      => $x->company,
                'position'     => $x->position,
                'start_date'   => optional($x->start_date)?->format('Y-m-d'),
                'end_date'     => optional($x->end_date)?->format('Y-m-d'),
                'functions'    => $x->functions,
                'is_non_formal'=> $x->is_non_formal,
            ])->values(),
            // relaciones para compatibilidad (ya como catálogo)
            'competencies' => $competenciesRel->map(fn ($c) => [
                'id'   => $c->id,
                'slug' => $c->slug,
                'name' => $c->nombre ?? $c->name,
            ])->values(),
            'languages' => $languagesRel->map(fn ($l) => [
                'id'    => $l->id,
                'code'  => $l->codigo ?? $l->code,
                'name'  => $l->nombre ?? $l->name,
                'level' => $l->pivot->nivel ?? null,
            ])->values(),
        ];
    }

    protected function toPublicResource(Student $s): array
    {
        return $this->toResource($s);
    }
}
