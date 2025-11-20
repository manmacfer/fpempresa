<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['nombre', 'classroom_number', 'teacher_id'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function teachers()
    {
        // Un classroom puede tener varios profesores asignados
        return $this->hasMany(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Obtener classroom por su número personalizado
     */
    public static function findByNumber($number)
    {
        return static::where('classroom_number', $number)->first();
    }
}
