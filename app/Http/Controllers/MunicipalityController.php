<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MunicipalityController extends Controller
{
    /**
     * Buscar municipios por término de búsqueda
     * GET /api/municipalities/search?q={query}
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = min((int) $request->input('limit', 20), 100);
        
        if (strlen($query) < 2) {
            return response()->json([
                'data' => [],
                'message' => 'La búsqueda debe tener al menos 2 caracteres',
            ]);
        }
        
        $municipalities = Municipality::search($query, $limit);
        
        return response()->json([
            'data' => $municipalities,
        ]);
    }
    
    /**
     * Obtener municipios de una provincia
     * GET /api/municipalities/by-province/{provinceCode}
     */
    public function byProvince(string $provinceCode): JsonResponse
    {
        $municipalities = Province::municipalities($provinceCode);
        
        return response()->json([
            'data' => $municipalities,
        ]);
    }
    
    /**
     * Listar todas las provincias
     * GET /api/provinces
     */
    public function provinces(): JsonResponse
    {
        $provinces = Province::getAll();
        
        return response()->json([
            'data' => $provinces,
        ]);
    }
    
    /**
     * Buscar provincias por nombre
     * GET /api/provinces/search?q={query}
     */
    public function searchProvinces(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = min((int) $request->input('limit', 10), 50);
        
        if (strlen($query) < 2) {
            return response()->json([
                'data' => Province::getAll()->take($limit),
            ]);
        }
        
        $provinces = Province::search($query, $limit);
        
        return response()->json([
            'data' => $provinces,
        ]);
    }
}
