<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matching extends Model
{
    use HasFactory;

    protected $fillable = ['student_id','company_id','status'];

    protected $casts = [
        'status' => \App\Enums\MatchingStatus::class,
    ];

    public function student() { return $this->belongsTo(\App\Models\Student::class); }
    public function company() { return $this->belongsTo(\App\Models\Company::class); }
}
