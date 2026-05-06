<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/health', [HealthController::class, 'index']);

// Auth routes
Route::prefix('auth')->group(function () {
   Route::post('/register', [AuthController::class, 'register']); 
   Route::post('/login', [AuthController::class, 'login']);
   Route::middleware('jwt.auth')->group(function () {
       Route::get('/me', [AuthController::class, 'me']);
       Route::post('/logout', [AuthController::class, 'logout']);
   });
});

// Protected routes
Route::middleware('jwt.auth')->group(function(){
    
    Route::prefix('users')->group(function (){
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::get('/{user}/profile', [UserController::class, 'profile']);
        Route::post('/{user}/follow/{targetUser}', [UserController::class, 'follow']);
        Route::post('/{user}/unfollow/{targetUser}', [UserController::class, 'unfollow']);
        Route::get('/{user}/followers', [UserController::class, 'followers']);
        Route::get('/{user}/following', [UserController::class, 'following']);
        Route::put('/{user}/profile', [UserController::class, 'updateProfile']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    Route::resource('categories', CategoryController::class)
    ->only(['index', 'store', 'update', 'destroy']);
});