<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacancy extends Model
{
    protected $fillable = [
        'title',
        'description',
        'cycle_required',
        'mode',
        'city',
        'province',
        'hours_per_week',
        'duration_weeks',
        'paid',
        'salary_month',
        'status',
        'tech_stack',
        'soft_skills',
        'company_id',
        'published_at',
    ];

    protected $casts = [
        'paid'          => 'boolean',
        'salary_month'  => 'integer',
        'tech_stack'    => 'array',
        'soft_skills'   => 'array',
        'published_at'  => 'datetime',
        'accepts_fp_general' => 'boolean',
        'accepts_fp_dual'    => 'boolean',

    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Idiomas requeridos (pivot con meta)
    public function requiredLanguages()
    {
        return $this->belongsToMany(\App\Models\Language::class, 'vacante_idioma_req', 'vacancy_id', 'language_id')
            ->withPivot('min_level');
    }


    // Competencias técnicas requeridas (si ya existe pivot vacante_competencia_req)
    public function requiredCompetencies()
    {
        return $this->belongsToMany(Competency::class, 'vacante_competencia_req', 'vacancy_id', 'competency_id');
    }
}
