<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Personales / contacto
        'dni','phone','birth_date','address','postal_code','city','has_driver_license','has_vehicle',
        // Académicos
        'cycle','center','year_start','year_end','fp_modality',
        // Disponibilidad
        'availability_slot','commitments','relocate','relocate_cities','transport_own','work_modality','remote_days','days_per_week','available_from',
        // Intereses / perfil
        'sectors','preferred_company_type','non_formal_experience','tech_competencies','soft_skills','languages','certifications',
        // Extra
        'hobbies','clubs','align_activities','entrepreneurship',
        // Links
        'link_linkedin','link_portfolio','link_github','link_video',
        // Archivos
        'cv_path','cover_letter_path','other_cert_paths','avatar_path',
    ];

    protected $casts = [
        'birth_date'         => 'date',
        'available_from'     => 'date',
        'has_driver_license' => 'boolean',
        'has_vehicle'        => 'boolean',
        'commitments'        => 'array',
        'relocate'           => 'boolean',
        'relocate_cities'    => 'array',
        'transport_own'      => 'boolean',
        'remote_days'        => 'integer',
        'days_per_week'      => 'integer',
        'sectors'            => 'array',
        'tech_competencies'  => 'array',
        'soft_skills'        => 'array',
        'languages'          => 'array',   // [{name, level}]
        'certifications'     => 'array',
        'align_activities'   => 'boolean',
        'other_cert_paths'   => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(StudentEducation::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(StudentExperience::class);
    }
}