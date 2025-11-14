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
        'birth_date'        => 'date',
        'available_from'    => 'date',
        'has_driver_license' => 'boolean',
        'has_vehicle'       => 'boolean',
        'relocate'          => 'boolean',
        'transport_own'     => 'boolean',
        'align_activities'  => 'boolean',
        'year_start'        => 'integer',
        'year_end'          => 'integer',
        'remote_days'       => 'integer',
        'days_per_week'     => 'integer',
        'commitments'       => 'array',
        'relocate_cities'   => 'array',
        'sectors'           => 'array',
        'tech_competencies' => 'array',
        'soft_skills'       => 'array',
        'languages'         => 'array',
        'certifications'    => 'array',
        'other_certs_paths' => 'array',
        'work_modalities' => 'array',
        'tech_competencies' => 'array',
        'languages' => 'array',
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

    // Accessors
    public function getAvatarUrlAttribute()
    {
        return $this->avatar_path ? Storage::url($this->avatar_path) : null;
    }


    public function languages()
    {
        return $this->belongsToMany(\App\Models\Language::class, 'alumno_idioma', 'student_id', 'language_id')
            ->withPivot(['nivel']);
    }

    public function competencies()
    {
        return $this->belongsToMany(\App\Models\Competency::class, 'alumno_competencia', 'student_id', 'competency_id');
    }
}
