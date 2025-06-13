<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DiaInhabilController;
use App\Http\Controllers\TramiteSolicitanteController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\Formularios\DomicilioController;

// Ruta principal - solo para usuarios no autenticados
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
});

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
        
    // Rutas de reset de contraseña
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Ruta de logout (requiere autenticación)
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout');
});

// Rutas de dashboard protegidas por autenticación y verificación de email
Route::middleware(['auth', \App\Http\Middleware\VerifyUserStatus::class])->group(function () {
    // Dashboard principal - accesible para todos los usuarios autenticados
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
        ->name('dashboard');

    // Rutas de gestión de roles (solo para administradores)
    Route::middleware(['auth', 'can:dashboard.admin'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

});

// Rutas de verificación de email (sin autenticación)
Route::get('/verificar-email/{id}/{token}', [VerificationController::class, 'verify'])
    ->name('verification.verify');
Route::post('/reenviar-verificacion', [VerificationController::class, 'resend'])
    ->name('verification.resend');

Route::get('/provider-dashboard', function () {
    return view('provider-dashboard');
})->name('provider.dashboard');

Route::middleware(['auth', \App\Http\Middleware\VerifyUserStatus::class])->group(function () {
    Route::resource('users', UserController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites.index');
    Route::get('/tramites/create', [TramiteController::class, 'create'])->name('tramites.create');
    Route::post('/tramites', [TramiteController::class, 'store'])->name('tramites.store');
    
    Route::resource('documentos', DocumentoController::class);
    
    // Rutas de Proveedores
    Route::resource('proveedores', ProveedorController::class);

    // Rutas de Perfil
    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Rutas de Citas
    Route::resource('citas', CitaController::class);
    Route::put('/citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado.update');

    Route::resource('dias-inhabiles', DiaInhabilController::class)->only(['create', 'store', 'destroy']);

    // Rutas de Revisión de Trámites
    Route::get('/revision', [RevisionController::class, 'index'])->name('revision.index');
    Route::get('/revision/{tramite}', [RevisionController::class, 'show'])->name('revision.show');
});

// Rutas para trámites
Route::prefix('tramites')->group(function () {
    Route::get('/', [TramiteController::class, 'index'])->name('tramites.index');
    Route::post('/iniciar', [TramiteController::class, 'iniciarTramite'])->name('tramites.iniciar');
    Route::get('/datos-generales', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'index'])->name('tramites.datos-generales');
    Route::post('/guardar-datos-generales', [TramiteController::class, 'guardarDatosGenerales'])->name('tramites.guardar-datos-generales');
    
    Route::get('/domicilio', [TramiteController::class, 'mostrarDomicilio'])->name('tramites.domicilio');
    Route::post('/guardar-domicilio', [TramiteController::class, 'guardarDomicilio'])->name('tramites.guardar-domicilio');
    Route::post('/guardar-domicilio-formulario', [DomicilioController::class, 'guardarFormulario'])->name('tramites.guardar-domicilio-formulario');
    
    // Rutas específicas para cada tipo de trámite
    Route::get('/{tipo_tramite}/{tramite}/create', [TramiteController::class, 'create'])
        ->where('tipo_tramite', 'inscripcion|renovacion|actualizacion')
        ->name('tramites.create');
    
    Route::post('/store', [TramiteController::class, 'store'])->name('tramites.store');
});

// Rutas para el módulo de Trámite Solicitante
Route::middleware(['auth'])->prefix('tramites-solicitante')->group(function () {
    Route::get('/', [TramiteSolicitanteController::class, 'index'])->name('tramites.solicitante.index');
    
    // Rutas POST para iniciar trámites
    Route::post('/iniciar-inscripcion', [TramiteSolicitanteController::class, 'iniciarInscripcion'])->name('tramites.solicitante.iniciar-inscripcion');
    Route::post('/iniciar-renovacion', [TramiteSolicitanteController::class, 'iniciarRenovacion'])->name('tramites.solicitante.iniciar-renovacion');
    Route::post('/iniciar-actualizacion', [TramiteSolicitanteController::class, 'iniciarActualizacion'])->name('tramites.solicitante.iniciar-actualizacion');
    
    // Rutas GET para redireccionar si alguien accede directamente
    Route::get('/iniciar-inscripcion', function() {
        return redirect()->route('tramites.solicitante.index');
    });
    Route::get('/iniciar-renovacion', function() {
        return redirect()->route('tramites.solicitante.index');
    });
    Route::get('/iniciar-actualizacion', function() {
        return redirect()->route('tramites.solicitante.index');
    });
    
    // Nuevas rutas para obtener datos dinámicamente
    Route::get('/datos-tramite', [TramiteSolicitanteController::class, 'obtenerDatosTramite'])->name('tramites.solicitante.datos-tramite');
    Route::get('/documentos', [TramiteSolicitanteController::class, 'obtenerDocumentos'])->name('tramites.solicitante.documentos');
    Route::post('/upload-documento', [TramiteSolicitanteController::class, 'subirDocumento'])->name('tramites.solicitante.upload-documento');
    Route::post('/finalizar', [TramiteSolicitanteController::class, 'finalizarTramite'])->name('tramites.solicitante.finalizar');
});
