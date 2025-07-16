<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VerificationController;

// Página principal para usuarios no autenticados
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
});

// MÓDULO DE AUTENTICACIÓN Y REGISTRO
Route::middleware(['web', 'guest'])->group(function () {
    // INICIAR SESIÓN
    Route::get('/iniciar-sesion', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/iniciar-sesion', [LoginController::class, 'login']);
    // REGISTRO DE USUARIOS
    Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/registro', [RegisterController::class, 'register']);
    // RECUPERACIÓN DE CONTRASEÑA
    Route::get('/recuperar-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/recuperar-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    // RESET DE CONTRASEÑA
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// CERRAR SESIÓN (requiere autenticación)
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout');
});

// VERIFICACIÓN DE EMAIL
Route::get('/verificar-email/{id}/{token}', [VerificationController::class, 'verify'])
    ->name('verification.verify');
Route::post('/reenviar-verificacion', [VerificationController::class, 'resend'])
    ->name('verification.resend');

// DASHBOARD (solo autenticados)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// USERS (solo autenticados)
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
});

// Notificaciones (solo autenticados, solo vista)
Route::middleware(['auth'])->get('/notificaciones', [App\Http\Controllers\NotificacionController::class, 'index'])->name('notificaciones.index');

// Perfil de usuario (solo autenticados)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
});

// Roles (solo autenticados)
Route::middleware(['auth'])->group(function () {
    Route::get('/roles', [App\Http\Controllers\RolesController::class, 'index'])->name('roles.index');
});

// Tramites (solo autenticados)
Route::middleware(['auth'])->get('/tramites', [App\Http\Controllers\TramiteController::class, 'index'])->name('tramites.index');

