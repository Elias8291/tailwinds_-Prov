<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DocumentoController;

// Ruta principal
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Grupo de rutas para usuarios no autenticados
Route::middleware(['web', 'guest'])->group(function () {
    // Rutas de autenticación
    Route::get('/iniciar-sesion', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/iniciar-sesion', [LoginController::class, 'login']);
    
    // Rutas de registro
    Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/registro', [RegisterController::class, 'register']);
    
    // Rutas de recuperación de contraseña
    Route::get('/recuperar-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/recuperar-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
});

// Ruta de logout (requiere autenticación)
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout');
});

// Rutas de dashboard protegidas por autenticación y permisos
Route::middleware(['auth'])->group(function () {
    // Dashboard administrativo
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
        ->middleware(['can:dashboard.admin'])
        ->name('dashboard');

    // Dashboard de solicitante
    Route::get('/dashboard2', [DashboardController::class, 'solicitanteDashboard'])
        ->middleware(['can:dashboard.solicitante'])
        ->name('dashboard2');

    // Rutas de gestión de roles (solo para administradores)
    Route::middleware(['auth', 'can:dashboard.admin'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::get('/email/verify', [VerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

Route::get('/provider-dashboard', function () {
    return view('provider-dashboard');
})->name('provider.dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class);
});

Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites.index');
Route::get('/tramites/create', [TramiteController::class, 'create'])->name('tramites.create');
Route::post('/tramites', [TramiteController::class, 'store'])->name('tramites.store');

Route::resource('documentos', DocumentoController::class);
