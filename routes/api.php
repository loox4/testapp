<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ToDoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

 

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/me', function(Request $request) { return auth()->user();});
        
        Route::get('/todo/read', [ToDoController::class, 'read']);
        Route::post('/todo/create', [ToDoController::class, 'create']);
        Route::post('/todo/update', [ToDoController::class, 'update']);
        Route::post('/todo/delete', [ToDoController::class, 'delete']);
        Route::post('/todo/reorder', [ToDoController::class, 'reorder']);
    
    });