<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Company extends Model
{
use HasFactory;


protected $guarded = ['id', 'created_at', 'updated_at'];


protected $casts = [
'sectors' => 'array',
];


protected $appends = ['avatar_url'];


public function user() { return $this->belongsTo(User::class); }


public function getAvatarUrlAttribute()
{
return $this->avatar_path ? Storage::url($this->avatar_path) : null;
}
}