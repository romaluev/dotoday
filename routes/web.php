<?php

use Illuminate\Support\Facades\Route;

// Web routes
Route::get('/', function () {
    return response()->json(['message' => 'DoToday API', 'version' => '1.0.0']);
});
