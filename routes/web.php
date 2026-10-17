<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatchReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informes/parches', [PatchReportController::class, 'show'])->name('reports.patches');
Route::post('/informes/parches/zap-scan', [PatchReportController::class, 'scan'])
    ->middleware('throttle:2,1')
    ->name('reports.patches.zap-scan');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::get('/dashboard/{module}', [CmsController::class, 'module'])
        ->whereIn('module', ['usuarios', 'contenidos', 'multimedia', 'seguridad', 'planeacion', 'evidencias'])
        ->name('cms.module');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
