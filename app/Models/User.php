<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_STUDENT   = 'STUDENT';
    public const ROLE_COMPANY   = 'COMPANY';
    public const ROLE_PROFESSOR = 'PROFESSOR';
    public const ROLE_ADMIN     = 'ADMIN';

    protected $fillable = ['name', 'email', 'password', 'role'];

    // Role helpers
    public function isAdmin(): bool     { return $this->role === self::ROLE_ADMIN; }
    public function isProfessor(): bool { return $this->role === self::ROLE_PROFESSOR; }
    public function isCompany(): bool   { return $this->role === self::ROLE_COMPANY; }
    public function isStudent(): bool   { return $this->role === self::ROLE_STUDENT; }

    // Owners (adjust namespaces if needed)
    public function student()
    {
        return $this->hasOne(\App\Models\Student::class);
    }

    public function company()
    {
        return $this->hasOne(\App\Models\Company::class);
    }
}
