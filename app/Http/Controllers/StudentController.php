<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Competency;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function editMe(Request $request)
    {
        $user = $request->user();

        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $user->first_name ?? $user->name,
                'last_name'  => $user->last_name,
                'cycle'      => null,
            ]
        );

        $student->load([
            'educations'  => fn ($q) => $q->orderBy('start_date', 'desc'),
            'experiences' => fn ($q) => $q->orderBy('start_date', 'desc'),
            'competencies',
            'languages',
        ]);

        // Catálogos (fallbacks si la BDD aún usa columnas en español)
        $competencyNameCol = Schema::hasColumn('competencies', 'name')
            ? 'name'
            : (Schema::hasColumn('competencies', 'nombre') ? 'nombre' : 'slug');

        $competencies = Competency::query()
            ->select('id', 'slug', DB::raw("$competencyNameCol as name"))
            ->orderBy($competencyNameCol)
            ->get();

        $languageCodeCol = Schema::hasColumn('languages', 'code')
            ? 'code'
            : (Schema::hasColumn('languages', 'codigo') ? 'codigo' : 'id');

        $languageNameCol = Schema::hasColumn('languages', 'name')
            ? 'name'
            : (Schema::hasColumn('languages', 'nombre') ? 'nombre' : 'id');

        $languages = Language::query()
            ->select('id', DB::raw("$languageCodeCol as code"), DB::raw("$languageNameCol as name"))
            ->orderBy($languageNameCol)
            ->get();

        return Inertia::render('Students/Edit', [
            'student'         => $this->toResource($student),
            'allCompetencies' => $competencies,
            'allLanguages'    => $languages,
        ]);
    }

    public function updateMe(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => $request->user()->id]);

        Log::info('students.update.me payload (raw)', $request->all());

        // Campos que pueden llegar como JSON string
        $jsonFields = [
            'commitments',
            'relocate_cities',
            'sectors',
            'tech_competencies',
            'tech_stack', // alias posible del front
            'soft_skills',
            'certifications',
            'languages',
            'competency_ids',
            'work_modalities',
        ];

        $raw = $request->all();

        foreach ($jsonFields as $f) {
            if (isset($raw[$f]) && is_string($raw[$f])) {
                $decoded = json_decode($raw[$f], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if ($decoded === null) {
                        $decoded = [];
                    }
                    $raw[$f] = $decoded;
                }
            }
        }

        // Alias: tech_stack → tech_competencies
        if (isset($raw['tech_stack']) && ! isset($raw['tech_competencies'])) {
            $raw['tech_competencies'] = $raw['tech_stack'];
        }

        // Alias: municipality (front) → city (columna real BDD)
        if (array_key_exists('municipality', $raw) && ! array_key_exists('city', $raw)) {
            $raw['city'] = $raw['municipality'];
        }

        // Normalizar availability_slot: tratar "" como null
        if (array_key_exists('availability_slot', $raw) && $raw['availability_slot'] === '') {
            $raw['availability_slot'] = null;
        }

        $request->replace($raw);

        // Validación
        $data = $request->validate([
            // personales / contacto
            'first_name'         => ['nullable', 'string', 'max:255'],
            'last_name'          => ['nullable', 'string', 'max:255'],
            'dni'                => ['nullable', 'string', 'max:50'],
            'phone'              => ['nullable', 'string', 'max:50'],
            'birth_date'         => ['nullable', 'date'],
            'address'            => ['nullable', 'string', 'max:255'],
            'postal_code'        => ['nullable', 'string', 'max:20'],
            'city'               => ['nullable', 'string', 'max:100'],
            'province'           => ['nullable', 'string', 'max:100'],
            'has_driver_license' => ['nullable', 'boolean'],
            'has_vehicle'        => ['nullable', 'boolean'],

            // académicos
            'cycle'              => ['nullable', 'string', 'max:50'],
            'center'             => ['nullable', 'string', 'max:255'],
            'year_start'         => ['nullable', 'integer'],
            'year_end'           => ['nullable', 'integer'],
            'fp_modality'        => ['nullable', 'in:dual,general'],

            // disponibilidad
            // OJO: aquí quito el "in:morning,afternoon,both" para que no reviente
            'availability_slot'  => ['nullable', 'string'],
            'commitments'        => ['nullable', 'array'],
            'commitments.*'      => ['string'],
            'relocate'           => ['nullable', 'boolean'],
            'relocate_cities'    => ['nullable', 'array'],
            'relocate_cities.*'  => ['string'],
            'transport_own'      => ['nullable', 'boolean'],
            'work_modality'      => ['nullable', 'in:presencial,remota,hibrida,indiferente'],
            'work_modalities'    => ['nullable', 'array'],
            'work_modalities.*'  => ['string'],
            'remote_days'        => ['nullable', 'integer', 'min:0', 'max:7'],
            'days_per_week'      => ['nullable', 'integer', 'min:1', 'max:7'],
            'available_from'     => ['nullable', 'date'],

            // intereses / perfil
            'sectors'                 => ['nullable', 'array'],
            'sectors.*'               => ['string'],
            'preferred_company_type'  => ['nullable', 'string', 'max:255'],
            'non_formal_experience'   => ['nullable', 'string'],

            // Competencias técnicas libres
            'tech_competencies'       => ['nullable', 'array'],
            'tech_competencies.*'     => ['string'],
            'soft_skills'             => ['nullable', 'array'],
            'soft_skills.*'           => ['string'],

            // Idiomas (payload para pivot)
            'languages'               => ['nullable', 'array'],
            'languages.*.language_id' => ['required_with:languages', 'integer', 'exists:languages,id'],
            'languages.*.level'       => ['required_with:languages', 'string'],

            // Certificaciones
            'certifications'          => ['nullable', 'array'],
            'certifications.*'        => ['string'],

            // Competencias (catálogo por IDs)
            'competency_ids'          => ['nullable', 'array'],
            'competency_ids.*'        => ['integer', 'exists:competencies,id'],

            // Links
            'link_linkedin'           => ['nullable', 'url'],
            'link_portfolio'          => ['nullable', 'url'],
            'link_github'             => ['nullable', 'url'],
            'link_video'              => ['nullable', 'url'],

            // Ficheros (opcionales)
            'avatar'                  => ['nullable', 'image', 'max:2048'],
            'cv'                      => ['nullable', 'file', 'max:5120'],
            'cover_letter'            => ['nullable', 'file', 'max:5120'],
            'other_certs.*'           => ['nullable', 'file', 'max:5120'],
        ]);

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

        // Extraemos relaciones especiales
        $competencyIds   = $data['competency_ids'] ?? null;
        $languagePayload = $data['languages'] ?? null;

        unset($data['competency_ids'], $data['languages']);

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
                // mantenemos la columna pivot 'nivel' por compatibilidad con la BDD actual
                $syncData[$row['language_id']] = ['nivel' => $row['level']];
            }
            $student->languages()->sync($syncData);
        }

        return redirect()->route('students.edit.me')->with('success', 'Perfil guardado.');
    }

    public function publicMe(Request $request)
    {
        $student = Student::where('user_id', $request->user()->id)->firstOrFail();

        return $this->publicShow($student);
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
        $competenciesRel = $s->relationLoaded('competencies')
            ? $s->getRelation('competencies')
            : $s->competencies()->get();

        $languagesRel = $s->relationLoaded('languages')
            ? $s->getRelation('languages')
            : $s->languages()->get();

        return [
            'id'                   => $s->id,
            'user_id'              => $s->user_id,
            'user_name'            => optional($s->user)->name,

            // personales
            'first_name'           => $s->first_name,
            'last_name'            => $s->last_name,
            'dni'                  => $s->dni,
            'phone'                => $s->phone,
            'birth_date'           => optional($s->birth_date)?->format('Y-m-d'),
            'address'              => $s->address,
            'postal_code'          => $s->postal_code,
            'province'             => $s->province,
            'city'                 => $s->city,
            'municipality'         => $s->city, // alias para el front
            'has_driver_license'   => $s->has_driver_license,
            'has_vehicle'          => $s->has_vehicle,

            // académicos
            'cycle'                => $s->cycle,
            'center'               => $s->center,
            'year_start'           => $s->year_start,
            'year_end'             => $s->year_end,
            'fp_modality'          => $s->fp_modality,

            // disponibilidad
            'availability_slot'    => $s->availability_slot,
            'commitments'          => $s->commitments ?? [],
            'relocate'             => $s->relocate,
            'relocate_cities'      => $s->relocate_cities ?? [],
            'transport_own'        => $s->transport_own,
            'work_modality'        => $s->work_modality,
            'work_modalities'      => $s->work_modalities ?? [],
            'remote_days'          => $s->remote_days,
            'days_per_week'        => $s->days_per_week,
            'available_from'       => optional($s->available_from)?->format('Y-m-d'),

            // intereses / perfil
            'sectors'              => $s->sectors ?? [],
            'preferred_company_type'=> $s->preferred_company_type,
            'non_formal_experience'=> $s->non_formal_experience,

            // competencias técnicas / blandas
            'tech_competencies'    => $s->tech_competencies ?? [],
            'tech_stack'           => $s->tech_competencies ?? [], // alias para front
            'soft_skills'          => $s->soft_skills ?? [],
            'certifications'       => $s->certifications ?? [],
            'other_certs_paths'    => $s->other_certs_paths ?? [],

            // extra
            'hobbies'              => $s->hobbies,
            'clubs'                => $s->clubs,
            'align_activities'     => $s->align_activities,
            'entrepreneurship'     => $s->entrepreneurship,

            // links
            'link_linkedin'        => $s->link_linkedin,
            'link_portfolio'       => $s->link_portfolio,
            'link_github'          => $s->link_github,
            'link_video'           => $s->link_video,

            // ficheros
            'avatar_url'           => $s->avatar_url,
            'cv_path'              => $s->cv_path,
            'cover_letter_path'    => $s->cover_letter_path,

            // relaciones
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

            'competencies' => $competenciesRel->map(fn ($c) => [
                'id'   => $c->id,
                'slug' => $c->slug,
                'name' => $c->name ?? $c->nombre,
            ])->values(),

            'languages' => $languagesRel->map(fn ($l) => [
                'id'    => $l->id,
                'code'  => $l->code ?? $l->codigo,
                'name'  => $l->name ?? $l->nombre,
                'level' => $l->pivot->level ?? $l->pivot->nivel ?? null,
            ])->values(),
        ];
    }

    protected function toPublicResource(Student $s): array
    {
        // De momento, usamos la misma estructura que en el editor.
        // Si luego quieres ocultar campos en público, los quitamos de aquí.
        return $this->toResource($s);
    }
}