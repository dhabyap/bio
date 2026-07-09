<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EditorController;

Route::get('/', function () {
    return view('links');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/track-click', [LinkController::class, 'track']);

Route::get('/analytics', [LinkController::class, 'index'])
    ->middleware('auth');

Route::get('/editor', [EditorController::class, 'index'])
    ->middleware('auth');
Route::post('/editor', [EditorController::class, 'update'])
    ->middleware('auth');