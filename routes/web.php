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
// DASHBOARD PRINCIPAL
// ============================================================================

Route::middleware(['auth', \App\Http\Middleware\VerifyUserStatus::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
});

// ============================================================================
// MÓDULO DE ADMINISTRACIÓN - ROLES Y PERMISOS
// ============================================================================

Route::middleware(['auth', 'can:dashboard.admin'])->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');           // Listar roles
    Route::get('/create', [RoleController::class, 'create'])->name('roles.create');   // Formulario crear
    Route::post('/', [RoleController::class, 'store'])->name('roles.store');          // Guardar nuevo
    Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');       // Ver detalle
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');  // Formulario editar
    Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');   // Actualizar
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy'); // Eliminar
});

// ============================================================================
// MÓDULO DE ADMINISTRACIÓN - USUARIOS
// ============================================================================

Route::middleware(['auth', \App\Http\Middleware\VerifyUserStatus::class])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');           // Listar usuarios
    Route::get('/create', [UserController::class, 'create'])->name('users.create');   // Formulario crear
    Route::post('/', [UserController::class, 'store'])->name('users.store');          // Guardar nuevo
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');       // Ver detalle
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');  // Formulario editar
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');   // Actualizar
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy'); // Eliminar
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

Route::middleware(['auth'])->prefix('tramites-solicitante')->group(function () {
    
    // DASHBOARD DEL SOLICITANTE
    Route::get('/', [TramiteSolicitanteController::class, 'index'])
        ->name('tramites.solicitante.index');
    
    // INICIAR NUEVOS TRÁMITES
    Route::post('/iniciar-inscripcion', [TramiteSolicitanteController::class, 'iniciarInscripcion'])
        ->name('tramites.solicitante.iniciar-inscripcion');
    Route::post('/iniciar-renovacion', [TramiteSolicitanteController::class, 'iniciarRenovacion'])
        ->name('tramites.solicitante.iniciar-renovacion');
    Route::post('/iniciar-actualizacion', [TramiteSolicitanteController::class, 'iniciarActualizacion'])
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
        ->name('tramites.solicitante.subir-constancia-fiscal');
    
    // OBTENER DATOS DINÁMICAMENTE
    Route::get('/datos-tramite', [TramiteSolicitanteController::class, 'obtenerDatosTramite'])
        ->name('tramites.solicitante.datos-tramite');
    
    // GESTIÓN DE DOCUMENTOS
    Route::get('/documentos', [TramiteSolicitanteController::class, 'obtenerDocumentos'])
        ->name('tramites.solicitante.documentos');
    Route::post('/upload-documento', [DocumentosController::class, 'subir'])
        ->name('tramites.solicitante.upload-documento');
    Route::get('/ver-documento/{tramite}/{documento}', [DocumentosController::class, 'verDocumento'])
        ->name('tramites.solicitante.ver-documento');
    
    // FINALIZAR TRÁMITES
    Route::post('/finalizar-tramite', [DocumentosController::class, 'finalizarTramite'])
        ->name('tramites.solicitante.finalizar-tramite');
    Route::post('/finalizar', [TramiteSolicitanteController::class, 'finalizarTramite'])
        ->name('tramites.solicitante.finalizar');
    
    // ESTADO Y SEGUIMIENTO DE TRÁMITES
    Route::get('/estado/{tramite}', [TramiteSolicitanteController::class, 'mostrarEstadoTramite'])
        ->name('tramites.solicitante.estado');
    Route::post('/habilitar-edicion/{tramite}', [TramiteSolicitanteController::class, 'habilitarEdicion'])
        ->name('tramites.solicitante.habilitar-edicion');
    Route::post('/corregir-seccion/{tramite}/{seccion}', [TramiteSolicitanteController::class, 'corregirSeccion'])
        ->name('tramites.solicitante.corregir-seccion');
    
    // API PARA DATOS DE DOMICILIO
    Route::get('/api/domicilio/{tramite}', [TramiteSolicitanteController::class, 'obtenerDatosDomicilioAPI'])
        ->name('tramites.solicitante.api.domicilio');
});

// ============================================================================
// MÓDULO DE REVISIÓN DE TRÁMITES
// ============================================================================

Route::middleware(['auth'])->prefix('revision')->group(function () {
    
    // LISTADO Y GESTIÓN DE REVISIONES
    Route::get('/', [RevisionController::class, 'index'])->name('revision.index');
    Route::get('/{tramite}', [RevisionController::class, 'show'])->name('revision.show');
    
    // REVISIÓN DE DATOS GENERALES
    Route::get('/{tramite}/datos-generales', [\App\Http\Controllers\DatosGeneralesController::class, 'prepararDatosRevision'])
        ->name('revision.datos-generales');
    
    // ACCIONES DE REVISIÓN POR SECCIÓN
    Route::post('/{tramite}/seccion/{seccion}/aprobar', [\App\Http\Controllers\SeccionRevisionController::class, 'aprobarSeccion'])
        ->name('revision.seccion.aprobar');
    Route::post('/{tramite}/seccion/{seccion}/rechazar', [\App\Http\Controllers\SeccionRevisionController::class, 'rechazarSeccion'])
        ->name('revision.seccion.rechazar');
    
    // ACCIONES MASIVAS DE REVISIÓN
    Route::post('/{tramite}/aprobar-todo', [\App\Http\Controllers\SeccionRevisionController::class, 'aprobarTodo'])
        ->name('revision.aprobar-todo');
    Route::post('/{tramite}/rechazar-todo', [\App\Http\Controllers\SeccionRevisionController::class, 'rechazarTodo'])
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

Route::middleware(['auth'])->prefix('documentos')->group(function () {
    Route::get('/', [DocumentoController::class, 'index'])->name('documentos.index');           // Listar documentos
    Route::get('/create', [DocumentoController::class, 'create'])->name('documentos.create');   // Formulario crear
    Route::post('/', [DocumentoController::class, 'store'])->name('documentos.store');          // Guardar nuevo
    Route::get('/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');  // Ver detalle
    Route::get('/{documento}/edit', [DocumentoController::class, 'edit'])->name('documentos.edit'); // Formulario editar
    Route::put('/{documento}', [DocumentoController::class, 'update'])->name('documentos.update'); // Actualizar
    Route::delete('/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy'); // Eliminar
});

// ============================================================================
// MÓDULO DE GESTIÓN - PROVEEDORES
// ============================================================================

Route::middleware(['auth'])->prefix('proveedores')->group(function () {
    Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');           // Listar proveedores
    Route::get('/create', [ProveedorController::class, 'create'])->name('proveedores.create');   // Formulario crear
    Route::post('/', [ProveedorController::class, 'store'])->name('proveedores.store');          // Guardar nuevo
    Route::get('/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show');  // Ver detalle
    Route::get('/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit'); // Formulario editar
    Route::put('/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update'); // Actualizar
    Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy'); // Eliminar
});

// ============================================================================
// MÓDULO DE GESTIÓN - CITAS Y CALENDARIO
// ============================================================================

Route::middleware(['auth'])->prefix('citas')->group(function () {
    
    // CRUD DE CITAS
    Route::get('/', [CitaController::class, 'index'])->name('citas.index');           // Listar citas
    Route::get('/create', [CitaController::class, 'create'])->name('citas.create');   // Formulario crear
    Route::post('/', [CitaController::class, 'store'])->name('citas.store');          // Guardar nueva
    Route::get('/{cita}', [CitaController::class, 'show'])->name('citas.show');       // Ver detalle
    Route::get('/{cita}/edit', [CitaController::class, 'edit'])->name('citas.edit');  // Formulario editar
    Route::put('/{cita}', [CitaController::class, 'update'])->name('citas.update');   // Actualizar
    Route::delete('/{cita}', [CitaController::class, 'destroy'])->name('citas.destroy'); // Eliminar
    
    // CAMBIAR ESTADO DE CITAS
    Route::put('/{cita}/estado', [CitaController::class, 'cambiarEstado'])
        ->name('citas.estado.update');
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

Route::middleware(['auth'])->prefix('perfil')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
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


