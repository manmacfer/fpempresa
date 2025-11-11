<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StudentController extends Controller
{
    /**
     * EDITAR MI PERFIL (sin ID)
     */
    public function editMe(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => Auth::id()]);

        return Inertia::render('Students/Edit', [
            'student' => $this->toResource($student),
        ]);
    }

    /**
     * ACTUALIZAR MI PERFIL (sin ID)
     */
    public function updateMe(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => Auth::id()]);
        $data = $this->validated($request);

        // Avatar opcional
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_path'] = $path;
        }

        $student->fill($data)->save();

        return back()->with('success', 'Perfil actualizado.');
    }

    /**
     * EDITAR POR ID (útil para admin o si mantienes la ruta resource)
     */
    public function edit(Student $student)
    {
        $this->ensureOwnerOrAbort($student);

        return Inertia::render('Students/Edit', [
            'student' => $this->toResource($student),
        ]);
    }

    /**
     * ACTUALIZAR POR ID (útil para admin o si mantienes la ruta resource)
     */
    public function update(Request $request, Student $student)
    {
        $this->ensureOwnerOrAbort($student);
        $data = $this->validated($request);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_path'] = $path;
        }

        $student->fill($data)->save();

        return back()->with('success', 'Perfil actualizado.');
    }

    /**
     * PERFIL PÚBLICO
     */
    public function publicShow(Student $student)
    {
        return Inertia::render('Students/PublicShow', [
            'student' => $this->toResource($student),
        ]);
    }

    /**
     * Validación de TODOS los campos del nuevo perfil
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            // PERSONALES / CONTACTO
            'phone'              => ['nullable','string','max:30'],
            'dni'                => ['nullable','string','max:32'],
            'birth_date'         => ['nullable','date'],
            'address'            => ['nullable','string','max:255'],
            'postal_code'        => ['nullable','string','max:12'],
            'city'               => ['nullable','string','max:190'],
            'has_driver_license' => ['nullable','boolean'],
            'has_vehicle'        => ['nullable','boolean'],

            // ACADÉMICOS (ciclo actual)
            'cycle'              => ['nullable','string','max:190'],
            'center'             => ['nullable','string','max:190'],
            'year_start'         => ['nullable','integer','between:1990,2100'],
            'year_end'           => ['nullable','integer','between:1990,2100'],
            'fp_modality'        => ['nullable','in:dual,general'],

            // DISPONIBILIDAD
            'availability_slot'  => ['nullable','in:morning,afternoon,both'],
            'commitments'        => ['nullable','array'],
            'commitments.*'      => ['nullable','string','max:190'],
            'relocate'           => ['nullable','boolean'],
            'relocate_cities'    => ['nullable','array'],
            'relocate_cities.*'  => ['nullable','string','max:190'],
            'transport_own'      => ['nullable','boolean'],
            'work_modality'      => ['nullable','in:presencial,remota,hibrida'],
            'remote_days'        => ['nullable','integer','between:0,7'],
            'days_per_week'      => ['nullable','integer','between:1,7'],
            'available_from'     => ['nullable','date'],

            // INTERESES / COMPETENCIAS / IDIOMAS
            'sectors'               => ['nullable','array'],
            'sectors.*'             => ['nullable','string','max:190'],
            'preferred_company_type'=> ['nullable','string','max:190'],
            'non_formal_experience' => ['nullable','string'],

            'tech_competencies'  => ['nullable','array'],
            'tech_competencies.*'=> ['nullable','string','max:190'],
            'soft_skills'        => ['nullable','array'],
            'soft_skills.*'      => ['nullable','string','max:190'],

            'languages'          => ['nullable','array'], // [{name,level}]
            'languages.*.name'   => ['required_with:languages','string','max:100'],
            'languages.*.level'  => ['nullable','string','max:50'],

            'certifications'     => ['nullable','array'],
            'certifications.*'   => ['nullable','string','max:190'],

            // EXTRA / LINKS
            'hobbies'            => ['nullable','string'],
            'clubs'              => ['nullable','string'],
            'motivation'         => ['nullable','string'],
            'limitations'        => ['nullable','string'],

            'link_linkedin'      => ['nullable','url','max:255'],
            'link_portfolio'     => ['nullable','url','max:255'],
            'link_github'        => ['nullable','url','max:255'],
            'link_video'         => ['nullable','url','max:255'],

            // ARCHIVOS (para el futuro; avatar ya activo)
            'avatar'             => ['nullable','image','max:2048'],
            // 'cv_pdf'          => ['nullable','mimetypes:application/pdf','max:4096'],
            // 'cover_letter_pdf'=> ['nullable','mimetypes:application/pdf','max:4096'],
            // 'other_certs.*'   => ['nullable','file','max:4096'],
        ]);
    }

    /**
     * Serialización uniforme para el front
     */
    private function toResource(Student $s): array
    {
        return [
            'id'                   => $s->id,
            'name'                 => optional($s->user)->name,
            'email'                => optional($s->user)->email,
            'avatar_url'           => $s->avatar_path ? Storage::url($s->avatar_path) : null,

            // PERSONALES / CONTACTO
            'phone'                => $s->phone,
            'dni'                  => $s->dni,
            'birth_date'           => optional($s->birth_date)?->format('Y-m-d'),
            'address'              => $s->address,
            'postal_code'          => $s->postal_code,
            'city'                 => $s->city,
            'has_driver_license'   => $s->has_driver_license,
            'has_vehicle'          => $s->has_vehicle,

            // ACADÉMICOS
            'cycle'                => $s->cycle,
            'center'               => $s->center,
            'year_start'           => $s->year_start,
            'year_end'             => $s->year_end,
            'fp_modality'          => $s->fp_modality, // dual | general

            // DISPONIBILIDAD
            'availability_slot'    => $s->availability_slot, // morning | afternoon | both
            'commitments'          => $s->commitments ?? [],
            'relocate'             => $s->relocate,
            'relocate_cities'      => $s->relocate_cities ?? [],
            'transport_own'        => $s->transport_own,
            'work_modality'        => $s->work_modality,     // presencial | remota | hibrida
            'remote_days'          => $s->remote_days,
            'days_per_week'        => $s->days_per_week,
            'available_from'       => optional($s->available_from)?->format('Y-m-d'),

            // INTERESES / COMPETENCIAS / IDIOMAS
            'sectors'              => $s->sectors ?? [],
            'preferred_company_type'=> $s->preferred_company_type,
            'non_formal_experience'=> $s->non_formal_experience,
            'tech_competencies'    => $s->tech_competencies ?? [],
            'soft_skills'          => $s->soft_skills ?? [],
            'languages'            => $s->languages ?? [],
            'certifications'       => $s->certifications ?? [],

            // EXTRA / LINKS
            'hobbies'              => $s->hobbies,
            'clubs'                => $s->clubs,
            'motivation'           => $s->motivation,
            'limitations'          => $s->limitations,

            'link_linkedin'        => $s->link_linkedin,
            'link_portfolio'       => $s->link_portfolio,
            'link_github'          => $s->link_github,
            'link_video'           => $s->link_video,

            // RELACIONES para CV (lectura; CRUD lo haremos luego)
            'educations'           => $s->educations()
                                        ->orderBy('start_date','desc')
                                        ->get(['id','title','center','start_date','end_date'])
                                        ->map(fn($e) => [
                                            'id'         => $e->id,
                                            'title'      => $e->title,
                                            'center'     => $e->center,
                                            'start_date' => optional($e->start_date)?->format('Y-m-d'),
                                            'end_date'   => optional($e->end_date)?->format('Y-m-d'),
                                        ]),
            'experiences'          => $s->experiences()
                                        ->orderBy('start_date','desc')
                                        ->get(['id','company','position','start_date','end_date','functions','is_non_formal'])
                                        ->map(fn($e) => [
                                            'id'          => $e->id,
                                            'company'     => $e->company,
                                            'position'    => $e->position,
                                            'start_date'  => optional($e->start_date)?->format('Y-m-d'),
                                            'end_date'    => optional($e->end_date)?->format('Y-m-d'),
                                            'functions'   => $e->functions,
                                            'is_non_formal'=> (bool)$e->is_non_formal,
                                        ]),
        ];
    }

    /**
     * Restringe edición a su propio perfil (ajusta a tu política/roles si quieres)
     */
    private function ensureOwnerOrAbort(Student $student): void
    {
        if ($student->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
