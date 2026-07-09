<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\LandingController;

// Landing page
Route::get('/', [LandingController::class, 'index']);

// Registration
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Public user link page
Route::get('/u/{username}', [LinkController::class, 'show']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/analytics', [LinkController::class, 'index']);
    Route::post('/analytics/clear-test', [LinkController::class, 'clearTest']);
    Route::get('/editor', [EditorController::class, 'index']);
    Route::post('/editor', [EditorController::class, 'update']);
    Route::post('/editor/ai-generate', [EditorController::class, 'aiGenerate']);
});

// Tracking (no CSRF needed)
Route::post('/track-click', [LinkController::class, 'track']);