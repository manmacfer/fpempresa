<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory;

    // Permite asignar todo salvo claves/sellos de tiempo
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'birth_date'         => 'date',
        'available_from'     => 'date',

        'validated'          => 'boolean',
        'has_driver_license' => 'boolean',
        'has_vehicle'        => 'boolean',
        'relocate'           => 'boolean',
        'transport_own'      => 'boolean',
        'align_activities'   => 'boolean',

        'year_start'    => 'integer',
        'year_end'      => 'integer',
        'remote_days'   => 'integer',
        'days_per_week' => 'integer',

        'commitments'       => 'array',
        'relocate_cities'   => 'array',
        'sectors'           => 'array',
        'tech_competencies' => 'array',
        'soft_skills'       => 'array',
        'languages'         => 'array',
        'certifications'    => 'array',
        'other_certs_paths' => 'array',
        'work_modalities'   => 'array',
    ];

    protected $appends = ['avatar_url'];

    // Relaciones
    public function educations()
    {
        return $this->hasMany(StudentEducation::class);
    }

    public function experiences()
    {
        return $this->hasMany(StudentExperience::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        // El profesor se obtiene a través del classroom
        return $this->hasOneThrough(
            Teacher::class,
            Classroom::class,
            'id',           // Foreign key on classrooms table
            'id',           // Foreign key on teachers table
            'classroom_id', // Local key on students table
            'teacher_id'    // Local key on classrooms table
        );
    }

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'alumno_competencia');
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'alumno_idioma')
            ->withPivot('nivel');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // Accessor avatar
    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        // Si ya es una URL absoluta, la devolvemos tal cual
        if (preg_match('~^https?://~', $this->avatar_path)) {
            return $this->avatar_path;
        }

        // Si es un path relativo en storage/app/public/...
        return asset('storage/' . $this->avatar_path);
    }
}
