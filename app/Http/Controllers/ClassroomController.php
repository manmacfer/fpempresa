<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Listar todos los números de classroom disponibles
     */
    public function list()
    {
        $classrooms = Classroom::select('classroom_number')
            ->orderBy('classroom_number', 'asc')
            ->pluck('classroom_number');

        return response()->json($classrooms);
    }
}
