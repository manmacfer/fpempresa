<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'legal_name',
        'trade_name',
        'cif',
        'sector',
        'website',
        'linkedin',
        'phone',
        'city',
        'province',
        'postal_code',
        'contact_name',
        'contact_email',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
