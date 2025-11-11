<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['user_id', 'name', 'cif', 'ubicacion', 'web', 'contact'];
    protected $casts = ['contact' => 'array'];
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }
}
