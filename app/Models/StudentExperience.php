<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'company',
        'position',
        'start_date',
        'end_date',
        'functions',     // responsabilidades / logros
        'is_non_formal', // true si es voluntariado/colaboración
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'is_non_formal'=> 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
