<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'full_name', 'ciclo', 'ubicacion', 'disponibilidad', 'extras'];
    protected $casts = ['extras' => 'array'];
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
    
}
