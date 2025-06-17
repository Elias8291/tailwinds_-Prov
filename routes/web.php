<?php

use Illuminate\Support\Facades\Route;

// ============================================================================
// CONTROLADORES - IMPORTACIONES ORGANIZADAS
// ============================================================================

// Controladores de Autenticación
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// Controladores Principales
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\VerificationController;

// Controladores de Gestión
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DiaInhabilController;

// Controladores de Trámites
use App\Http\Controllers\TramiteSolicitanteController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\TramiteNavegacionController;

// Controladores de Formularios
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\Formularios\DocumentosController;
use App\Http\Controllers\DireccionController;

// Controladores de API
use App\Http\Controllers\API\SectorController;

// ============================================================================
// RUTAS PÚBLICAS (Sin autenticación requerida)
// ============================================================================

// Página principal para usuarios no autenticados
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
});

// ============================================================================
// MÓDULO DE AUTENTICACIÓN Y REGISTRO
// ============================================================================

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

// ============================================================================
// DASHBOARD
// ============================================================================
Route::middleware(['auth', 'can:dashboard.admin,dashboard.solicitante,dashboard.revisor'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ============================================================================
// MÓDULO DE ADMINISTRACIÓN - ROLES Y PERMISOS
// ============================================================================

Route::middleware(['auth', 'can:roles.ver'])->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/create', [RoleController::class, 'create'])->middleware('can:roles.crear')->name('roles.create');
    Route::post('/', [RoleController::class, 'store'])->middleware('can:roles.crear')->name('roles.store');
    Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('can:roles.editar')->name('roles.edit');
    Route::put('/{role}', [RoleController::class, 'update'])->middleware('can:roles.editar')->name('roles.update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('can:roles.eliminar')->name('roles.destroy');
});

// ============================================================================
// MÓDULO DE ADMINISTRACIÓN - USUARIOS
// ============================================================================

Route::middleware(['auth', 'can:usuarios.ver'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->middleware('can:usuarios.crear')->name('users.create');
    Route::post('/', [UserController::class, 'store'])->middleware('can:usuarios.crear')->name('users.store');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('can:usuarios.editar')->name('users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->middleware('can:usuarios.editar')->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('can:usuarios.eliminar')->name('users.destroy');
    Route::post('/{user}/assign-role', [UserController::class, 'assignRole'])->middleware('can:usuarios.asignar-roles')->name('users.assign-role');
});

// ============================================================================
// MÓDULO DE ADMINISTRACIÓN - LOGS DEL SISTEMA
// ============================================================================

Route::middleware(['auth', 'can:logs.ver'])->prefix('logs')->group(function () {
    Route::get('/', [LogController::class, 'index'])->name('logs.index');
    Route::get('/{log}', [LogController::class, 'show'])->name('logs.show');
    Route::delete('/{log}', [LogController::class, 'destroy'])->middleware('can:logs.eliminar')->name('logs.destroy');
});

// ============================================================================
// MÓDULO DE GESTIÓN - TRÁMITES ADMINISTRATIVOS
// ============================================================================

Route::middleware(['auth'])->prefix('tramites')->group(function () {
    
    // LISTADO Y GESTIÓN ADMINISTRATIVA (REDIRIGIR AL PORTAL DEL SOLICITANTE)
    Route::get('/', function() {
        return redirect()->route('tramites.solicitante.index');
    })->name('tramites.index');
    Route::post('/', [TramiteController::class, 'store'])->name('tramites.store');
    
    // TÉRMINOS Y CONDICIONES
    Route::get('/terminos-condiciones', [TramiteController::class, 'mostrarTerminos'])
        ->name('tramites.terminos');
    Route::post('/iniciar', [TramiteController::class, 'iniciarTramite'])
        ->name('tramites.iniciar');
    
    // FORMULARIOS DE TRÁMITES
    Route::get('/datos-generales', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'index'])
        ->name('tramites.datos-generales');
    Route::post('/guardar-datos-generales', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'guardar'])
        ->name('tramites.guardar-datos-generales');
    
    Route::get('/domicilio', [TramiteController::class, 'mostrarDomicilio'])
        ->name('tramites.domicilio');
    Route::post('/guardar-domicilio', [TramiteController::class, 'guardarDomicilio'])
        ->name('tramites.guardar-domicilio');
    Route::post('/guardar-domicilio-formulario', [DireccionController::class, 'guardarFormulario'])
        ->name('tramites.guardar-domicilio-formulario');
    Route::post('/guardar-constitucion-formulario', [ConstitucionController::class, 'guardarFormulario'])
        ->name('tramites.guardar-constitucion-formulario');
    Route::post('/guardar-accionistas-formulario', [\App\Http\Controllers\Formularios\AccionistasController::class, 'guardarFormulario'])
        ->name('tramites.guardar-accionistas-formulario');
    Route::post('/guardar-apoderado-formulario', [\App\Http\Controllers\Formularios\ApoderadoLegalController::class, 'guardarFormulario'])
        ->name('tramites.guardar-apoderado-formulario');
    
    // RUTAS ESPECÍFICAS POR TIPO DE TRÁMITE
    Route::get('/{tipo_tramite}/{tramite}/create', [TramiteController::class, 'create'])
        ->where('tipo_tramite', 'inscripcion|renovacion|actualizacion')
        ->name('tramites.create.tipo');
    
    // NAVEGACIÓN ENTRE PASOS
    Route::get('/{tramite}/paso/{paso}', [TramiteNavegacionController::class, 'mostrarPaso'])
        ->where(['tramite' => '[0-9]+', 'paso' => '[0-9]+'])
        ->name('tramites.solicitante.paso');
    
    Route::match(['get', 'post'], '/{tramite}/siguiente/{paso}', [TramiteNavegacionController::class, 'siguientePaso'])
        ->where(['tramite' => '[0-9]+', 'paso' => '[0-9]+'])
        ->name('tramites.navegacion.siguiente');
    
    Route::get('/{tramite}/anterior/{paso}', [TramiteNavegacionController::class, 'pasoAnterior'])
        ->where(['tramite' => '[0-9]+', 'paso' => '[0-9]+'])
        ->name('tramites.navegacion.anterior');
    
    Route::post('/store', [TramiteController::class, 'store'])->name('tramites.store');
});

// ============================================================================
// MÓDULO DE FORMULARIOS - DATOS GENERALES
// ============================================================================

Route::middleware(['auth'])->prefix('formularios')->group(function () {
    Route::post('/datos-generales/guardar', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'guardar'])
        ->name('datos-generales.guardar');
});

// ============================================================================
// MÓDULO PORTAL DEL SOLICITANTE - MIS TRÁMITES
// ============================================================================

Route::middleware(['auth', 'can:tramites-solicitante.ver'])->prefix('tramites-solicitante')->group(function () {
    
    // DASHBOARD DEL SOLICITANTE
    Route::get('/', [TramiteSolicitanteController::class, 'index'])
        ->name('tramites.solicitante.index');
    
    // INICIAR NUEVOS TRÁMITES
    Route::post('/iniciar-inscripcion', [TramiteSolicitanteController::class, 'iniciarInscripcion'])
        ->middleware('can:tramites-solicitante.inscripcion')
        ->name('tramites.solicitante.iniciar-inscripcion');
    Route::post('/iniciar-renovacion', [TramiteSolicitanteController::class, 'iniciarRenovacion'])
        ->middleware('can:tramites-solicitante.renovacion')
        ->name('tramites.solicitante.iniciar-renovacion');
    Route::post('/iniciar-actualizacion', [TramiteSolicitanteController::class, 'iniciarActualizacion'])
        ->middleware('can:tramites-solicitante.actualizacion')
        ->name('tramites.solicitante.iniciar-actualizacion');
    
    // REDIRECCIONES PARA ACCESO DIRECTO (GET)
    Route::get('/iniciar-inscripcion', function() {
        return redirect()->route('tramites.solicitante.index');
    });
    Route::get('/iniciar-renovacion', function() {
        return redirect()->route('tramites.solicitante.index');
    });
    Route::get('/iniciar-actualizacion', function() {
        return redirect()->route('tramites.solicitante.index');
    });
    
    // CONSTANCIA DE SITUACIÓN FISCAL
    Route::get('/constancia-fiscal/{tipo_tramite}/{tramite}', [TramiteSolicitanteController::class, 'mostrarConstanciaFiscal'])
        ->name('tramites.solicitante.constancia-fiscal');
    Route::post('/constancia-fiscal/subir', [TramiteSolicitanteController::class, 'subirConstanciaFiscal'])
        ->middleware('can:tramites-solicitante.subir-documentos')
        ->name('tramites.solicitante.subir-constancia-fiscal');
    
    // OBTENER DATOS DINÁMICAMENTE
    Route::get('/datos-tramite', [TramiteSolicitanteController::class, 'obtenerDatosTramite'])
        ->name('tramites.solicitante.datos-tramite');
    
    // GESTIÓN DE DOCUMENTOS
    Route::get('/documentos', [TramiteSolicitanteController::class, 'obtenerDocumentos'])
        ->name('tramites.solicitante.documentos');
    Route::post('/upload-documento', [DocumentosController::class, 'subir'])
        ->middleware('can:tramites-solicitante.subir-documentos')
        ->name('tramites.solicitante.upload-documento');
    Route::get('/ver-documento/{tramite}/{documento}', [DocumentosController::class, 'verDocumento'])
        ->name('tramites.solicitante.ver-documento');
    
    // FINALIZAR TRÁMITES
    Route::post('/finalizar-tramite', [DocumentosController::class, 'finalizarTramite'])
        ->middleware('can:tramites-solicitante.finalizar')
        ->name('tramites.solicitante.finalizar-tramite');
    Route::post('/finalizar', [TramiteSolicitanteController::class, 'finalizarTramite'])
        ->middleware('can:tramites-solicitante.finalizar')
        ->name('tramites.solicitante.finalizar');
    
    // ESTADO Y SEGUIMIENTO DE TRÁMITES
    Route::get('/estado/{tramite}', [TramiteSolicitanteController::class, 'mostrarEstadoTramite'])
        ->name('tramites.solicitante.estado');
    Route::post('/habilitar-edicion/{tramite}', [TramiteSolicitanteController::class, 'habilitarEdicion'])
        ->middleware('can:tramites-solicitante.editar')
        ->name('tramites.solicitante.habilitar-edicion');
    Route::post('/corregir-seccion/{tramite}/{seccion}', [TramiteSolicitanteController::class, 'corregirSeccion'])
        ->middleware('can:tramites-solicitante.editar')
        ->name('tramites.solicitante.corregir-seccion');
    
    // API PARA DATOS DE DOMICILIO
    Route::get('/api/domicilio/{tramite}', [TramiteSolicitanteController::class, 'obtenerDatosDomicilioAPI'])
        ->name('tramites.solicitante.api.domicilio');
});

// ============================================================================
// MÓDULO DE REVISIÓN DE TRÁMITES
// ============================================================================

Route::middleware(['auth', 'can:revision-tramites.ver'])->prefix('revision')->group(function () {
    
    // LISTADO Y GESTIÓN DE REVISIONES
    Route::get('/', [RevisionController::class, 'index'])->name('revision.index');
    Route::get('/{tramite}', [RevisionController::class, 'show'])->name('revision.show');
    
    // REVISIÓN DE DATOS GENERALES
    Route::get('/{tramite}/datos-generales', [\App\Http\Controllers\DatosGeneralesController::class, 'prepararDatosRevision'])
        ->name('revision.datos-generales');
    
    // ACCIONES DE REVISIÓN POR SECCIÓN
    Route::post('/{tramite}/seccion/{seccion}/aprobar', [\App\Http\Controllers\SeccionRevisionController::class, 'aprobarSeccion'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('revision.seccion.aprobar');
    Route::post('/{tramite}/seccion/{seccion}/rechazar', [\App\Http\Controllers\SeccionRevisionController::class, 'rechazarSeccion'])
        ->middleware('can:revision-tramites.rechazar')
        ->name('revision.seccion.rechazar');
    
    // ACCIONES MASIVAS DE REVISIÓN
    Route::post('/{tramite}/aprobar-todo', [\App\Http\Controllers\SeccionRevisionController::class, 'aprobarTodo'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('revision.aprobar-todo');
    Route::post('/{tramite}/rechazar-todo', [\App\Http\Controllers\SeccionRevisionController::class, 'rechazarTodo'])
        ->middleware('can:revision-tramites.rechazar')
        ->name('revision.rechazar-todo');
    Route::post('/{tramite}/pausar', [\App\Http\Controllers\SeccionRevisionController::class, 'pausarRevision'])
        ->name('revision.pausar');
    
    // ESTADO DE REVISIONES
    Route::get('/{tramite}/estado-revisiones', [\App\Http\Controllers\SeccionRevisionController::class, 'obtenerEstadoRevisiones'])
        ->name('revision.estado-revisiones');
});

// ============================================================================
// MÓDULO DE GESTIÓN - DOCUMENTOS
// ============================================================================

Route::middleware(['auth', 'can:documentos.ver'])->prefix('documentos')->group(function () {
    Route::get('/', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('/create', [DocumentoController::class, 'create'])->middleware('can:documentos.crear')->name('documentos.create');
    Route::post('/', [DocumentoController::class, 'store'])->middleware('can:documentos.crear')->name('documentos.store');
    Route::get('/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
    Route::get('/{documento}/edit', [DocumentoController::class, 'edit'])->middleware('can:documentos.editar')->name('documentos.edit');
    Route::put('/{documento}', [DocumentoController::class, 'update'])->middleware('can:documentos.editar')->name('documentos.update');
    Route::delete('/{documento}', [DocumentoController::class, 'destroy'])->middleware('can:documentos.eliminar')->name('documentos.destroy');
});

// ============================================================================
// MÓDULO DE GESTIÓN - PROVEEDORES
// ============================================================================

Route::middleware(['auth', 'can:proveedores.ver'])->prefix('proveedores')->group(function () {
    Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::get('/create', [ProveedorController::class, 'create'])->middleware('can:proveedores.crear')->name('proveedores.create');
    Route::post('/', [ProveedorController::class, 'store'])->middleware('can:proveedores.crear')->name('proveedores.store');
    Route::get('/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show');
    Route::get('/{proveedor}/edit', [ProveedorController::class, 'edit'])->middleware('can:proveedores.editar')->name('proveedores.edit');
    Route::put('/{proveedor}', [ProveedorController::class, 'update'])->middleware('can:proveedores.editar')->name('proveedores.update');
    Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])->middleware('can:proveedores.eliminar')->name('proveedores.destroy');
});

// ============================================================================
// MÓDULO DE GESTIÓN - CITAS Y CALENDARIO
// ============================================================================

Route::middleware(['auth', 'can:citas.ver'])->prefix('citas')->group(function () {
    
    // CRUD DE CITAS
    Route::get('/', [CitaController::class, 'index'])->name('citas.index');
    Route::get('/create', [CitaController::class, 'create'])->middleware('can:citas.crear')->name('citas.create');
    Route::post('/', [CitaController::class, 'store'])->middleware('can:citas.crear')->name('citas.store');
    Route::get('/{cita}', [CitaController::class, 'show'])->name('citas.show');
    Route::get('/{cita}/edit', [CitaController::class, 'edit'])->middleware('can:citas.editar')->name('citas.edit');
    Route::put('/{cita}', [CitaController::class, 'update'])->middleware('can:citas.editar')->name('citas.update');
    Route::delete('/{cita}', [CitaController::class, 'destroy'])->middleware('can:citas.eliminar')->name('citas.destroy');
    
    // CALENDARIO
    Route::get('/calendario', [CalendarioController::class, 'index'])->middleware('can:citas.calendario')->name('citas.calendario');
    Route::post('/agendar', [CitaController::class, 'agendar'])->middleware('can:citas.agendar')->name('citas.agendar');
    Route::post('/{cita}/cancelar', [CitaController::class, 'cancelar'])->middleware('can:citas.cancelar')->name('citas.cancelar');
});

// GESTIÓN DE DÍAS INHÁBILES
Route::middleware(['auth'])->prefix('dias-inhabiles')->group(function () {
    Route::get('/create', [DiaInhabilController::class, 'create'])->name('dias-inhabiles.create');
    Route::post('/', [DiaInhabilController::class, 'store'])->name('dias-inhabiles.store');
    Route::delete('/{diaInhabil}', [DiaInhabilController::class, 'destroy'])->name('dias-inhabiles.destroy');
});

// ============================================================================
// MÓDULO DE PERFIL DE USUARIO
// ============================================================================

Route::middleware(['auth', 'can:perfil.ver'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/edit', [ProfileController::class, 'edit'])->middleware('can:perfil.editar')->name('profile.edit');
    Route::put('/', [ProfileController::class, 'update'])->middleware('can:perfil.editar')->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->middleware('can:perfil.cambiar-password')->name('profile.password.update');
});

// ============================================================================
// DASHBOARD DE PROVEEDOR
// ============================================================================

Route::get('/provider-dashboard', function () {
    return view('provider-dashboard');
})->name('provider.dashboard');

// ============================================================================
// API ENDPOINTS
// ============================================================================

Route::prefix('api')->group(function () {
    
    // API DE SECTORES Y ACTIVIDADES
    Route::get('/sectores/{sector}/actividades', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'getActividadesPorSector']);
    Route::get('/actividades', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'getAllActividades']);
    Route::get('/actividades/{actividad}', [SectorController::class, 'getActividad']);
    
    // API DE DATOS GENERALES
    Route::get('/datos-generales/{tramite}', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'obtenerDatos'])
        ->name('api.datos-generales.obtener');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});


