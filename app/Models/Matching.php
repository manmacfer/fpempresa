<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matching extends Model
{
    protected $fillable = [
        'student_id', 
        'company_id', 
        'vacancy_id', 
        'status', 
        'validado_centro',
        'student_matched',
        'company_matched',
        'comentarios_centro'
    ];

    protected $casts = [
        'validado_centro' => 'boolean',
        'student_matched' => 'boolean',
        'company_matched' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function documents()
    {
        return $this->hasMany(MatchingDocument::class);
    }

    /**
     * Scope para obtener solo matches completos (ambos dieron match)
     */
    public function scopeCompleted($query)
    {
        return $query->where('student_matched', true)
                     ->where('company_matched', true);
    }

    /**
     * Scope para obtener matches validados por el centro
     */
    public function scopeValidated($query)
    {
        return $query->where('validado_centro', true);
    }

    /**
     * Scope para obtener matches pendientes de validación
     */
    public function scopePendingValidation($query)
    {
        return $query->completed()
                     ->where('validado_centro', false);
    }
}