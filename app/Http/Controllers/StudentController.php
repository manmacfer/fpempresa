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
    /**
     * Editar mi propio perfil de alumno
     */
    public function editMe(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => $request->user()->id]);

        // Cargamos TODO lo que afecta a la ficha y a la compatibilidad
        $student->load([
            'educations'  => fn ($q) => $q->orderBy('start_date', 'desc'),
            'experiences' => fn ($q) => $q->orderBy('start_date', 'desc'),
            // relaciones que usa la compatibilidad
            'competencies',
            'languages',
        ]);

        // Catálogos: los MISMOS que usan las vacantes
        $competencies = Competency::orderBy('name')->get(['id', 'name', 'slug']);
        $languages    = Language::orderBy('name')->get(['id', 'code', 'name']);

        return Inertia::render('Students/Edit', [
            'student'         => $this->toResource($student),
            'allCompetencies' => $competencies,
            'allLanguages'    => $languages,
        ]);
    }

    /**
     * Guardar mi perfil
     */
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
            'city'                => ['nullable', 'string', 'max:100'], // ← POBLACIÓN para cotejación
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

            // OJO: ahora el alumno puede marcar varias modalidades
            'work_modalities'     => ['nullable', 'array'],
            'work_modalities.*'   => ['in:presencial,remota,hibrida'],

            // campo “viejo” de una sola modalidad lo dejamos opcional, por compatibilidad
            'work_modality'       => ['nullable', 'in:presencial,remota,hibrida'],

            'remote_days'         => ['nullable', 'integer', 'min:0', 'max:7'],
            'days_per_week'       => ['nullable', 'integer', 'min:1', 'max:7'],
            'available_from'      => ['nullable', 'date'],

            // intereses / perfil (libres, sólo para mostrar en el perfil)
            'sectors'                 => ['nullable', 'array'],
            'sectors.*'               => ['string'],
            'preferred_company_type'  => ['nullable', 'string', 'max:255'],
            'non_formal_experience'   => ['nullable', 'string', 'max:255'],

            // Estas arrays son decorativas / CV, NO se usan en la cotejación
            'tech_competencies'       => ['nullable', 'array'],
            'tech_competencies.*'     => ['string'],
            'soft_skills'             => ['nullable', 'array'],
            'soft_skills.*'           => ['string'],
            'certifications'          => ['nullable', 'array'],
            'certifications.*'        => ['string'],

            // *** COMPETENCIAS para la COTEJACIÓN ***
            // (IDs de la tabla competencies)
            'competency_ids'          => ['nullable', 'array'],
            'competency_ids.*'        => ['integer', 'exists:competencies,id'],

            // *** IDIOMAS para la COTEJACIÓN ***
            // array de objetos { language_id, level }
            'language_items'                 => ['nullable', 'array'],
            'language_items.*.language_id'   => ['required_with:language_items', 'integer', 'exists:languages,id'],
            'language_items.*.level'         => ['nullable', 'string', 'max:10'],

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

        // Normalizamos la modalidad simple a partir de las múltiples (para no romper nada)
        if (empty($data['work_modality']) && !empty($data['work_modalities']) && is_array($data['work_modalities'])) {
            $data['work_modality'] = $data['work_modalities'][0];
        }

        // Subida de ficheros
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

        // Guardamos los campos "directos" de la tabla students
        $student->fill($data);
        $student->saveOrFail();

        // ==== SINCRONIZAR COMPETENCIAS (para la compatibilidad) ====
        if (array_key_exists('competency_ids', $data)) {
            $student->competencies()->sync($data['competency_ids'] ?? []);
        }

        // ==== SINCRONIZAR IDIOMAS (para la compatibilidad) ====
        if (array_key_exists('language_items', $data)) {
            $pivotData = [];

            foreach ($data['language_items'] ?? [] as $item) {
                if (!empty($item['language_id'])) {
                    $pivotData[$item['language_id']] = [
                        'nivel' => $item['level'] ?? null,
                    ];
                }
            }

            $student->languages()->sync($pivotData);
        }

        return redirect()
            ->route('students.edit.me')
            ->with('success', 'Perfil guardado.');
    }

    /**
     * Perfil público del alumno
     */
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

    // ================== Resources ==================

    protected function toResource(Student $s): array
    {
        return [
            'id'                  => $s->id,

            // personales
            'user_name'           => optional($s->user)->name,
            'dni'                 => $s->dni,
            'phone'               => $s->phone,
            'birth_date'          => optional($s->birth_date)?->format('Y-m-d'),
            'address'             => $s->address,
            'postal_code'         => $s->postal_code,
            'city'                => $s->city,   // ← aquí va la POBLACIÓN para la cotejación
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

            // modalidad única (legacy) + array de modalidades para la compatibilidad
            'work_modality'       => $s->work_modality,
            'work_modalities'     => $s->work_modalities ?? [],

            'remote_days'         => $s->remote_days,
            'days_per_week'       => $s->days_per_week,
            'available_from'      => optional($s->available_from)?->format('Y-m-d'),

            // intereses (texto libre, sólo mostrar)
            'sectors'                 => $s->sectors ?? [],
            'preferred_company_type'  => $s->preferred_company_type,
            'non_formal_experience'   => $s->non_formal_experience,
            'tech_competencies'       => $s->tech_competencies ?? [],
            'soft_skills'             => $s->soft_skills ?? [],
            'certifications'          => $s->certifications ?? [],

            // COMPETENCIAS DE COTEJACIÓN (IDs)
            'competency_ids' => $s->competencies
                ? $s->competencies->pluck('id')->values()
                : collect(),

            // IDIOMAS DE COTEJACIÓN (id + nivel)
            'language_items' => $s->languages
                ? $s->languages->map(fn ($lang) => [
                    'language_id' => $lang->id,
                    'code'        => $lang->code,
                    'name'        => $lang->name,
                    'level'       => $lang->pivot->nivel,
                ])->values()
                : collect(),

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

            // relaciones (formación y experiencia)
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
        ];
    }

    protected function toPublicResource(Student $s): array
    {
        // Por ahora devolvemos lo mismo; la vista pública ya se encarga de ocultar vacíos
        return $this->toResource($s);
    }
}
