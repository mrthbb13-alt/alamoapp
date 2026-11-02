<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MunicipalDashboardController;
use App\Http\Controllers\CitizenAppController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CitizenAuth;

// ====================================================
// AUTENTICACIÓN CIUDADANA Y ACCESO INICIAL
// ====================================================
Route::get('/', function () {
    return (Auth::check() || session('citizen_logged_in'))
        ? redirect()->route('citizen.home')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// ====================================================
// FASE 1: PLATAFORMA WEB GOBIERNO MUNICIPAL (BACKOFFICE / C2)
// ====================================================
Route::prefix('panel-municipal')->group(function () {
    Route::get('/', [MunicipalDashboardController::class, 'index'])->name('municipal.dashboard');
    Route::get('/api/poll', [MunicipalDashboardController::class, 'poll'])->name('municipal.poll');
    Route::post('/reportes/{id}/actualizar', [MunicipalDashboardController::class, 'updateStatus'])->name('municipal.update');
});

// ====================================================
// FASE 2: APLICACIÓN WEB CIUDADANA (PROTEGIDA CON LOGIN)
// ====================================================
Route::middleware(CitizenAuth::class)->prefix('reportar')->group(function () {
    // Vistas principales
    Route::get('/', [CitizenAppController::class, 'home'])->name('citizen.home');
    Route::get('/comunidad', [CitizenAppController::class, 'feed'])->name('citizen.feed');
    Route::get('/seguimiento', [CitizenAppController::class, 'track'])->name('citizen.track');

    // Apartado de Perfil Ciudadano
    Route::get('/perfil', [AuthController::class, 'profile'])->name('citizen.profile');
    Route::post('/perfil/actualizar', [AuthController::class, 'updateProfile'])->name('citizen.profile.update');

    // Endpoints JSON para acciones ciudadanas
    Route::post('/guardar', [CitizenAppController::class, 'store'])->name('citizen.store');
    Route::post('/apoyar/{id}', [CitizenAppController::class, 'upvote'])->name('citizen.upvote');
    Route::get('/api/cercanos', [CitizenAppController::class, 'checkNearby'])->name('citizen.nearby');
    Route::get('/api/folio/{folio}', [CitizenAppController::class, 'searchFolio'])->name('citizen.folio');
});
