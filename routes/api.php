<?php

use Illuminate\Support\Facades\Route;

/* API routes */
Route::middleware("api")->group(function () {
    // ejemplo: /api/ping
    Route::get("/ping", fn() => response()->json(["pong"=>true]));
});
