<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $fillable = [
        'code',
        'name',
        'province_code',
        'province_name',
    ];

    /**
     * Buscar municipios por nombre o provincia
     */
    public static function search(string $query, int $limit = 20)
    {
        return self::where('name', 'like', "%{$query}%")
            ->orWhere('province_name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'code', 'name', 'province_name']);
    }
}
