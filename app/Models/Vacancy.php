<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacancy extends Model
{
    protected $fillable = [
        'company_id',
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
        'tech_stack',
        'soft_skills',
        'status',
        'published_at',
    ];

    protected $casts = [
        'paid'         => 'boolean',
        'tech_stack'   => 'array',
        'soft_skills'  => 'array',
        'published_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
