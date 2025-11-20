<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\MunicipalityController;
use Illuminate\Support\Facades\Route;

/* API routes */
Route::middleware("api")->group(function () {
    // ejemplo: /api/ping
    Route::get("/ping", fn() => response()->json(["pong"=>true]));
    
    // Classrooms
    Route::get("/classrooms", [ClassroomController::class, 'list']);
    
    // Municipios y Provincias
    Route::get("/municipalities/search", [MunicipalityController::class, 'search']);
    Route::get("/municipalities/by-province/{provinceCode}", [MunicipalityController::class, 'byProvince']);
    Route::get("/provinces", [MunicipalityController::class, 'provinces']);
    Route::get("/provinces/search", [MunicipalityController::class, 'searchProvinces']);
});
