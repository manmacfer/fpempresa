<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function editMe(Request $request)
    {
        // Asegura que el registro existe con 'cycle' null si no viene
        $student = Student::firstOrCreate(['user_id' => Auth::id()]);

        return Inertia::render('Students/Edit', [
            'student' => $this->toResource($student),
        ]);
    }

    public function updateMe(Request $request)
    {
        $student = Student::firstOrCreate(['user_id' => Auth::id()]);
        $data = $this->validated($request);

        // Uploads
        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }
        if ($request->hasFile('cv')) {
            $data['cv_path'] = $request->file('cv')->store('documents/cv', 'public');
        }
        if ($request->hasFile('cover_letter')) {
            $data['cover_letter_path'] = $request->file('cover_letter')->store('documents/cover_letters', 'public');
        }
        if ($request->hasFile('other_certs')) {
            $paths = $student->other_cert_paths ?? [];
            foreach ((array)$request->file('other_certs') as $file) {
                $paths[] = $file->store('documents/certificates', 'public');
            }
            $data['other_cert_paths'] = $paths;
        }

        $student->fill($data)->save();

        return back()->with('success', 'Perfil actualizado.');
    }

    public function edit(Student $student)
    {
        $this->ensureOwnerOrAbort($student);
        return Inertia::render('Students/Edit', [
            'student' => $this->toResource($student),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $this->ensureOwnerOrAbort($student);
        $data = $this->validated($request);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }
        if ($request->hasFile('cv')) {
            $data['cv_path'] = $request->file('cv')->store('documents/cv', 'public');
        }
        if ($request->hasFile('cover_letter')) {
            $data['cover_letter_path'] = $request->file('cover_letter')->store('documents/cover_letters', 'public');
        }
        if ($request->hasFile('other_certs')) {
            $paths = $student->other_cert_paths ?? [];
            foreach ((array)$request->file('other_certs') as $file) {
                $paths[] = $file->store('documents/certificates', 'public');
            }
            $data['other_cert_paths'] = $paths;
        }

        $student->fill($data)->save();

        return back()->with('success', 'Perfil actualizado.');
    }

    public function publicShow(Student $student)
    {
        return Inertia::render('Students/PublicShow', [
            'student' => $this->toResource($student),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            // Personales / contacto
            'dni'                => ['nullable','string','max:32'],
            'phone'              => ['nullable','string','max:30'],
            'birth_date'         => ['nullable','date'],
            'address'            => ['nullable','string','max:255'],
            'postal_code'        => ['nullable','string','max:12'],
            'city'               => ['nullable','string','max:190'],
            'has_driver_license' => ['nullable','boolean'],
            'has_vehicle'        => ['nullable','boolean'],

            // Académicos
            'cycle'              => ['nullable','in:1 DAM,2 DAM,1 DAW,2 DAW'],
            'center'             => ['nullable','string','max:190'],
            'year_start'         => ['nullable','integer','between:1990,2100'],
            'year_end'           => ['nullable','integer','between:1990,2100'],
            'fp_modality'        => ['nullable','in:dual,general'],

            // Disponibilidad
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

            // Intereses / perfil
            'sectors'               => ['nullable','array'],
            'sectors.*'             => ['nullable','string','max:190'],
            'preferred_company_type'=> ['nullable','string','max:190'],
            'non_formal_experience' => ['nullable','string'],
            'tech_competencies'     => ['nullable','array'],
            'tech_competencies.*'   => ['nullable','string','max:190'],
            'soft_skills'           => ['nullable','array'],
            'soft_skills.*'         => ['nullable','string','max:190'],
            'languages'             => ['nullable','array'],
            'languages.*.name'      => ['required_with:languages','string','max:100'],
            'languages.*.level'     => ['nullable','string','max:50'],
            'certifications'        => ['nullable','array'],
            'certifications.*'      => ['nullable','string','max:190'],

            // Extra
            'hobbies'            => ['nullable','string'],
            'clubs'              => ['nullable','string'],
            'align_activities'   => ['nullable','boolean'],
            'entrepreneurship'   => ['nullable','string'],

            // Links
            'link_linkedin'      => ['nullable','url','max:255'],
            'link_portfolio'     => ['nullable','url','max:255'],
            'link_github'        => ['nullable','url','max:255'],
            'link_video'         => ['nullable','url','max:255'],

            // Archivos
            'avatar'             => ['nullable','image','max:2048'],
            'cv'                 => ['nullable','file','mimes:pdf,doc,docx','max:8192'],
            'cover_letter'       => ['nullable','file','mimes:pdf,doc,docx','max:8192'],
            'other_certs'        => ['nullable','array'],
            'other_certs.*'      => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:8192'],
        ]);
    }

    private function toResource(Student $s): array
    {
        return [
            'id'                   => $s->id,
            'name'                 => optional($s->user)->name,
            'email'                => optional($s->user)->email,
            'first_name'           => optional($s->user)->first_name,
            'last_name'            => optional($s->user)->last_name,
            'avatar_url'           => $s->avatar_path ? Storage::url($s->avatar_path) : null,

            // Personales
            'dni'                  => $s->dni,
            'phone'                => $s->phone,
            'birth_date'           => optional($s->birth_date)?->format('Y-m-d'),
            'address'              => $s->address,
            'postal_code'          => $s->postal_code,
            'city'                 => $s->city,
            'has_driver_license'   => $s->has_driver_license,
            'has_vehicle'          => $s->has_vehicle,

            // Académicos
            'cycle'                => $s->cycle,
            'center'               => $s->center,
            'year_start'           => $s->year_start,
            'year_end'             => $s->year_end,
            'fp_modality'          => $s->fp_modality,

            // Disponibilidad
            'availability_slot'    => $s->availability_slot,
            'commitments'          => $s->commitments ?? [],
            'relocate'             => $s->relocate,
            'relocate_cities'      => $s->relocate_cities ?? [],
            'transport_own'        => $s->transport_own,
            'work_modality'        => $s->work_modality,
            'remote_days'          => $s->remote_days,
            'days_per_week'        => $s->days_per_week,
            'available_from'       => optional($s->available_from)?->format('Y-m-d'),

            // Intereses / perfil
            'sectors'              => $s->sectors ?? [],
            'preferred_company_type'=> $s->preferred_company_type,
            'non_formal_experience'=> $s->non_formal_experience,
            'tech_competencies'    => $s->tech_competencies ?? [],
            'soft_skills'          => $s->soft_skills ?? [],
            'languages'            => $s->languages ?? [],
            'certifications'       => $s->certifications ?? [],

            // Extra
            'hobbies'              => $s->hobbies,
            'clubs'                => $s->clubs,
            'align_activities'     => $s->align_activities,
            'entrepreneurship'     => $s->entrepreneurship,

            // Links
            'link_linkedin'        => $s->link_linkedin,
            'link_portfolio'       => $s->link_portfolio,
            'link_github'          => $s->link_github,
            'link_video'           => $s->link_video,

            // Archivos
            'cv_url'               => $s->cv_path ? Storage::url($s->cv_path) : null,
            'cover_letter_url'     => $s->cover_letter_path ? Storage::url($s->cover_letter_path) : null,
            'other_certs_urls'     => collect($s->other_cert_paths ?? [])->map(fn($p) => Storage::url($p)),

            // Historial
            'educations'           => $s->educations()->orderBy('start_date','desc')->get(['id','title','center','start_date','end_date'])->map(fn($e) => [
                                        'id' => $e->id,
                                        'title' => $e->title,
                                        'center' => $e->center,
                                        'start_date' => optional($e->start_date)?->format('Y-m-d'),
                                        'end_date' => optional($e->end_date)?->format('Y-m-d'),
                                    ]),
            'experiences'          => $s->experiences()->orderBy('start_date','desc')->get(['id','company','position','start_date','end_date','functions','is_non_formal'])->map(fn($e) => [
                                        'id' => $e->id,
                                        'company' => $e->company,
                                        'position' => $e->position,
                                        'start_date' => optional($e->start_date)?->format('Y-m-d'),
                                        'end_date' => optional($e->end_date)?->format('Y-m-d'),
                                        'functions' => $e->functions,
                                        'is_non_formal' => (bool)$e->is_non_formal,
                                    ]),
        ];
    }

    private function ensureOwnerOrAbort(Student $student): void
    {
        if ($student->user_id !== Auth::id()) {
            abort(403);
        }
    }
}