<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'full_name', 'classroom_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classroom()
    {
        // Un profesor pertenece a un classroom
        return $this->belongsTo(Classroom::class);
    }

    public function students()
    {
        // Obtener todos los estudiantes de su classroom
        return $this->hasManyThrough(
            Student::class,
            Classroom::class,
            'id',           // Foreign key on classrooms table
            'classroom_id', // Foreign key on students table
            'classroom_id', // Local key on teachers table
            'id'            // Local key on classrooms table
        );
    }
}
