<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Province extends Model
{
    // No existe tabla separada, se calculan desde municipalities
    public $timestamps = false;
    
    /**
     * Obtener lista de provincias únicas desde municipalities
     */
    public static function getAll(): Collection
    {
        return Municipality::query()
            ->select('province_code', 'province_name')
            ->distinct()
            ->orderBy('province_name')
            ->get()
            ->map(function ($row) {
                return [
                    'code' => $row->province_code,
                    'name' => $row->province_name,
                ];
            });
    }
    
    /**
     * Buscar provincias por nombre
     */
    public static function search(string $query, int $limit = 10): Collection
    {
        return Municipality::query()
            ->select('province_code', 'province_name')
            ->where('province_name', 'like', "%{$query}%")
            ->distinct()
            ->orderBy('province_name')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'code' => $row->province_code,
                    'name' => $row->province_name,
                ];
            });
    }
    
    /**
     * Obtener municipios de una provincia
     */
    public static function municipalities(string $provinceCode): Collection
    {
        return Municipality::query()
            ->where('province_code', $provinceCode)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'province_name']);
    }
    
    /**
     * Verificar si dos provincias son la misma
     */
    public static function isSameProvince(?string $province1, ?string $province2): bool
    {
        if (!$province1 || !$province2) {
            return false;
        }
        
        return self::normalize($province1) === self::normalize($province2);
    }
    
    /**
     * Normalizar nombre de provincia
     */
    public static function normalize(?string $province): ?string
    {
        if (!$province) {
            return null;
        }
        
        $normalized = trim(mb_strtolower($province));
        
        // Remover artículos y normalizar casos especiales
        $normalized = preg_replace('/^(la\s+|las\s+|el\s+|los\s+)/', '', $normalized);
        
        // Normalizar acentos y caracteres especiales
        $normalized = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $normalized
        );
        
        return $normalized;
    }
}
