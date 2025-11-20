<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rejection extends Model
{
    protected $fillable = ['vacancy_id', 'student_id', 'reason'];
    
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
