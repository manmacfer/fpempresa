<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['vacancy_id', 'student_id', 'state', 'feedback'];
    
    protected $attributes = [
        'state' => 'enviada',
    ];
    
    protected $casts = ['feedback' => 'array'];
    
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
