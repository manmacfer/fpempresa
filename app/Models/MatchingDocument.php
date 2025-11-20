<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchingDocument extends Model
{
    protected $fillable = [
        'matching_id',
        'uploaded_by',
        'name',
        'type',
        'file_path'
    ];

    public function matching()
    {
        return $this->belongsTo(Matching::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
