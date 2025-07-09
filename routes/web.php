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
use App\Http\Controllers\CalendarioController;
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
use App\Http\Controllers\Api\SectorController;

// Controladores de Documentos
use App\Http\Controllers\DocumentoMembretadoController;
use App\Http\Controllers\MembretesController;

// Controladores de IA
// use App\Http\Controllers\AI\DocumentTrainingController;

// Controladores de Estado
use App\Http\Controllers\MiEstadoProveedorController;

// Controladores de Documentos
use App\Http\Controllers\DocumentoSolicitanteController;

// Controladores de Notificaciones
use App\Http\Controllers\NotificacionController;

// Controlador de Mis Trámites
use App\Http\Controllers\MisTramitesController;

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

// RUTA DE PRUEBA PARA VALIDACIONES (solo en desarrollo)
if (config('app.debug')) {
    Route::middleware(['auth'])->get('/test-validation', function () {
        return view('test-validation');
    })->name('test.validation');
}

// VERIFICACIÓN DE EMAIL
Route::get('/verificar-email/{id}/{token}', [VerificationController::class, 'verify'])
    ->name('verification.verify');
Route::post('/reenviar-verificacion', [VerificationController::class, 'resend'])
    ->name('verification.resend');

// ============================================================================
// DASHBOARD
// ============================================================================
Route::middleware(['auth'])->group(function () {
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
});

// ============================================================================
// MÓDULO DE FORMULARIOS - DATOS GENERALES
// ============================================================================

Route::middleware(['auth'])->prefix('formularios')->group(function () {
    Route::post('/datos-generales/guardar', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'guardar'])
        ->name('datos-generales.guardar');
    Route::get('/datos-generales/test', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'test'])
        ->name('datos-generales.test');
});

// Ruta de debug simple sin middleware para probar conectividad
Route::post('/debug/datos-generales', function(\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('=== DEBUG RUTA SIMPLE ===', [
        'method' => $request->method(),
        'url' => $request->url(),
        'data' => $request->all(),
        'headers' => $request->headers->all(),
        'user_authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'session_id' => session()->getId(),
        'csrf_token_valid' => csrf_token() === $request->input('_token')
    ]);
    
    // Validar campos básicos
    $errores = [];
    
    if (!$request->input('tramite_id')) {
        $errores[] = 'tramite_id requerido';
    }
    
    if (!$request->input('giro')) {
        $errores[] = 'giro requerido';
    }
    
    if (!$request->input('contacto_nombre')) {
        $errores[] = 'contacto_nombre requerido';
    }
    
    if (!$request->input('contacto_cargo')) {
        $errores[] = 'contacto_cargo requerido';
    }
    
    if (!$request->input('contacto_correo')) {
        $errores[] = 'contacto_correo requerido';
    }
    
    if (!$request->input('contacto_telefono')) {
        $errores[] = 'contacto_telefono requerido';
    }
    
    if (!$request->input('actividades_seleccionadas')) {
        $errores[] = 'actividades_seleccionadas requerido';
    }
    
    \Illuminate\Support\Facades\Log::info('Validación debug completada', [
        'errores_encontrados' => $errores,
        'total_errores' => count($errores)
    ]);
    
    if (empty($errores)) {
        // Si no hay errores, simular una redirección exitosa
        \Illuminate\Support\Facades\Log::info('✅ Validación exitosa - simulando redirección');
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Datos procesados correctamente',
                'next_step' => 2,
                'redirect_url' => '/tramites/inscripcion/' . $request->input('tramite_id')
            ]);
        }
        
        // Para formularios normales, redirigir
        return redirect('/tramites/inscripcion/' . $request->input('tramite_id'))
            ->with('success', 'Datos guardados correctamente');
    } else {
        // Si hay errores, mostrarlos
        \Illuminate\Support\Facades\Log::warning('❌ Errores de validación encontrados', $errores);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'errors' => $errores,
                'message' => 'Errores de validación encontrados'
            ], 422);
        }
        
        return back()->withErrors($errores)->withInput();
    }
})->name('debug.datos-generales');

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
    
    // ✅ RUTAS PARA ACTUALIZACIÓN DE PROVEEDORES ACTIVOS
    Route::get('/actualizacion/selector', [TramiteSolicitanteController::class, 'mostrarSelectorActualizacion'])
        ->middleware('can:tramites-solicitante.actualizacion')
        ->name('tramites.actualizacion.selector');
    Route::post('/actualizacion/seccion/{seccion}', [TramiteSolicitanteController::class, 'iniciarActualizacionSeccion'])
        ->middleware('can:tramites-solicitante.actualizacion')
        ->name('tramites.actualizacion.seccion');
    
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
    
    // RUTAS LOCALES SIMPLIFICADAS (nuevas)
    Route::get('/documentos-local', [\App\Http\Controllers\LocalDocumentoController::class, 'obtenerDocumentos'])
        ->name('tramites.solicitante.documentos-local');
    Route::post('/upload-documento-local', [\App\Http\Controllers\LocalDocumentoController::class, 'subirDocumento'])
        ->middleware('can:tramites-solicitante.subir-documentos')
        ->name('tramites.solicitante.upload-documento-local');
    Route::get('/ver-documento-local/{tramite}/{documento}', [\App\Http\Controllers\LocalDocumentoController::class, 'verDocumento'])
        ->name('tramites.solicitante.ver-documento-local');
    
    // RUTAS ORIGINALES (mantener compatibilidad)
            Route::post('/upload-documento', [TramiteSolicitanteController::class, 'subirDocumento'])
            ->middleware('can:tramites-solicitante.subir-documentos')
            ->name('tramites.solicitante.upload-documento');
        
        Route::post('/reemplazar-documento', [TramiteSolicitanteController::class, 'reemplazarDocumento'])
            ->middleware('can:tramites-solicitante.subir-documentos')
            ->name('tramites.solicitante.reemplazar-documento');
        
        Route::get('/estado-actualizado/{tramiteId}', [TramiteSolicitanteController::class, 'obtenerEstadoActualizado'])
            ->name('tramites.solicitante.estado-actualizado');
    Route::get('/ver-documento/{tramite}/{documento}', [DocumentosController::class, 'verDocumento'])
        ->name('tramites.solicitante.ver-documento');
    
    // VALIDACIÓN IA DE DOCUMENTOS
    Route::get('/validacion-ia', [TramiteSolicitanteController::class, 'obtenerValidacionIA'])
        ->name('tramites.solicitante.validacion-ia');
    
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

Route::prefix('revision')->name('revision.')->middleware(['auth', 'can:revision-tramites.ver'])->group(function () {
    // Rutas básicas de revisión
    Route::get('/', [RevisionController::class, 'index'])->name('index');
    Route::get('/get-next-pv', [RevisionController::class, 'getNextPV'])->name('get-next-pv');
    Route::get('/{tramite}', [RevisionController::class, 'show'])->name('show');
    
    // Rutas específicas para tipos de revisión
    Route::get('/{tramite}/digital', [RevisionController::class, 'revisionDigital'])->name('digital');
    Route::get('/{tramite}/presencial', [RevisionController::class, 'cotejo_presencial'])->name('presencial');
    
    // Ruta para verificación de identidad
    Route::get('/{tramite}/cotejo-presencial', [RevisionController::class, 'cotejo_presencial'])->name('presencial');
    
    // Rutas de acciones de revisión
    Route::post('/{tramite}/aprobar', [RevisionController::class, 'aprobarTodo'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('aprobar');
    
    Route::post('/{tramite}/generar-oficio', [RevisionController::class, 'generarOficio'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('generar-oficio');
    
    Route::post('/{tramite}/aprobar-todo', [RevisionController::class, 'aprobarTodo'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('aprobar-todo');
    
    Route::post('/{tramite}/rechazar', [RevisionController::class, 'rechazarTodo'])
        ->middleware('can:revision-tramites.rechazar')
        ->name('rechazar-todo');
    
    Route::post('/{tramite}/solicitar-correcciones', [RevisionController::class, 'solicitarCorrecciones'])
        ->middleware('can:revision-tramites.solicitar-correcciones')
        ->name('solicitar-correcciones');
    
    Route::post('/{tramite}/pausar', [RevisionController::class, 'pausarRevision'])
        ->middleware('can:revision-tramites.pausar')
        ->name('pausar');
    
    // Rutas de acciones por sección (permisos verificados en el controlador para AJAX)
    Route::post('/{tramite}/seccion/{seccion}/aprobar', [RevisionController::class, 'aprobarSeccion'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('aprobar-seccion');
    
    Route::post('/{tramite}/seccion/{seccion}/rechazar', [RevisionController::class, 'rechazarSeccion'])
        ->middleware('can:revision-tramites.rechazar')
        ->name('rechazar-seccion');
    
    // Ruta para agregar comentarios
    Route::post('/{tramite}/comentar', [RevisionController::class, 'agregarComentario'])
        ->middleware('can:revision-tramites.comentar')
        ->name('comentar');
    
    // AJAX endpoints para revisión avanzada
    Route::get('/{tramite}/estado-revisiones', [RevisionController::class, 'obtenerEstadoRevisiones'])
        ->name('estado-revisiones');
    
    Route::post('/{tramite}/seccion/{seccion}/comentario', [RevisionController::class, 'guardarComentarioSeccion'])
        ->middleware('can:revision-tramites.comentar')
        ->name('guardar-comentario-seccion');
    
    Route::get('/{tramite}/documentos-seccion', [RevisionController::class, 'getDocumentosSeccion'])
        ->name('documentos-seccion');
    
    // Ruta específica para ver documentos en revisión
    Route::get('/{tramite}/ver-documento/{documento}', [RevisionController::class, 'verDocumento'])
        ->name('ver-documento');
    
    // Rutas para aprobar/rechazar documentos individuales
    Route::post('/{tramite}/documento/{documento}/aprobar', [RevisionController::class, 'aprobarDocumento'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('aprobar-documento');
    Route::post('/{tramite}/documento/{documento}/rechazar', [RevisionController::class, 'rechazarDocumento'])
        ->middleware('can:revision-tramites.rechazar')
        ->name('rechazar-documento');

    // Nueva ruta para agendar citas desde revisión
    Route::post('/{tramite}/agendar-cita', [RevisionController::class, 'agendarCitaRevision'])
        ->middleware('can:citas.crear')
        ->name('agendar-cita');
    
    // Nueva ruta para terminar revisión digital
    Route::post('/{tramite}/terminar-revision-digital', [RevisionController::class, 'terminarRevisionDigital'])
        ->middleware('can:revision-tramites.aprobar')
        ->name('terminar-revision-digital');
});

// Rutas de revisión
Route::get('/revision/{tramite}/v2', [RevisionController::class, 'showV2'])->name('revision.v2');

// ============================================================================
// MÓDULO DE GESTIÓN - DOCUMENTOS
// ============================================================================

Route::middleware(['auth', 'can:documentos.ver'])->prefix('documentos')->group(function () {
    Route::get('/', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('/create', [DocumentoController::class, 'create'])->middleware('can:documentos.crear')->name('documentos.create');
    Route::post('/', [DocumentoController::class, 'store'])->middleware('can:documentos.crear')->name('documentos.store');
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
// MÓDULO DE GESTIÓN - MI ESTADO PROVEEDOR
// ============================================================================

Route::middleware(['auth', 'can:mi-estado-proveedor.ver'])->prefix('mi-estado-proveedor')->group(function () {
    Route::get('/', [MiEstadoProveedorController::class, 'index'])->name('mi-estado-proveedor.index');
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
    
    // RUTAS DE COTEJO FÍSICO
    Route::get('/cotejo', [CitaController::class, 'citasCotejo'])->middleware('can:revision-tramites.ver')->name('citas.cotejo');
Route::post('/citas/{cita}/completar-cotejo', [CitaController::class, 'completarCotejo'])->middleware('can:revision-tramites.aprobar')->name('citas.completar-cotejo');
      Route::get('/cotejo/dashboard', [CitaController::class, 'dashboardCotejo'])->middleware('can:revision-tramites.ver')->name('citas.cotejo.dashboard');
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
    
    // API DE SECTORES Y ACTIVIDADES (comentadas para evitar conflicto con api.php)
    // Route::get('/sectores/{sector}/actividades', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'getActividadesPorSector']);
    // Route::get('/actividades', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'getAllActividades']);
    // Route::get('/actividades/{actividad}', [SectorController::class, 'getActividad']);
    
    // API DE DATOS GENERALES
    Route::get('/datos-generales/{tramite}', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'obtenerDatos'])
        ->name('api.datos-generales.obtener');
    Route::get('/datos-generales/{tramite}/mostrar', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'mostrar'])
        ->name('datos-generales.mostrar');
});

// ============================================================================
// MÓDULO DE DOCUMENTOS MEMBRETADOS
// ============================================================================

Route::middleware(['auth'])->prefix('documento-membretado')->group(function () {
    // Formulario para crear documento
    Route::get('/', [DocumentoMembretadoController::class, 'formulario'])->name('documento.formulario');
    
    // Vista previa del documento en navegador
    Route::get('/vista', [DocumentoMembretadoController::class, 'vista'])->name('documento.vista');
    
    // Generar PDF personalizado
    Route::post('/generar-pdf', [DocumentoMembretadoController::class, 'generarPDF'])->name('documento.generar-pdf');
    
    // Ejemplo completo con datos predefinidos
    Route::get('/ejemplo', [DocumentoMembretadoController::class, 'ejemplo'])->name('documento.ejemplo');
});

// ============================================================================
// MÓDULO DE MEMBRETES OFICIALES
// ============================================================================

Route::middleware(['auth'])->prefix('membretes')->group(function () {
    // Página principal de membretes
    Route::get('/', [MembretesController::class, 'index'])->middleware('can:membretes.ver')->name('membretes.index');
    
    // Ejemplos de documentos (descarga directa)
    Route::get('/ejemplo/inscripcion', [MembretesController::class, 'ejemploInscripcion'])->name('membretes.ejemplo.inscripcion');
    Route::get('/ejemplo/renovacion', [MembretesController::class, 'ejemploRenovacion'])->name('membretes.ejemplo.renovacion');
    Route::get('/ejemplo/actualizacion', [MembretesController::class, 'ejemploActualizacion'])->name('membretes.ejemplo.actualizacion');
});

// ============================================================================
// RUTAS DE PRUEBA PARA PÁGINAS DE ERROR (Solo en desarrollo)
// ============================================================================

// ============================================================================
// MÓDULO DE ENTRENAMIENTO DE IA
// ============================================================================

// Route::middleware(['auth'])->prefix('ai/training')->name('ai.training.')->group(function () {
//     
//     // Dashboard principal del módulo de entrenamiento
//     Route::get('/', [DocumentTrainingController::class, 'index'])->name('index');
//     
//     // Subida de documentos para entrenamiento
//     Route::get('/upload', [DocumentTrainingController::class, 'uploadForm'])->name('upload');
//     Route::post('/upload', [DocumentTrainingController::class, 'upload'])->name('upload.store');
//     
//     // Revisión y aprobación de documentos
//     Route::get('/review', [DocumentTrainingController::class, 'review'])->name('review');
//     Route::post('/approve/{trainingData}', [DocumentTrainingController::class, 'approve'])->name('approve');
//     Route::post('/reject/{trainingData}', [DocumentTrainingController::class, 'reject'])->name('reject');
//     
//     // Entrenamiento de modelos
//     Route::post('/train', [DocumentTrainingController::class, 'trainModel'])->name('train');
//     
//     // Ver detalles de documento de entrenamiento
//     Route::get('/document/{trainingData}', [DocumentTrainingController::class, 'showDocument'])->name('document.show');
//     
//     // Servir archivos de entrenamiento
//     Route::get('/file/{trainingData}', [DocumentTrainingController::class, 'serveTrainingFile'])->name('file.serve');
//     
//     // Eliminar datos de entrenamiento
//     Route::delete('/training-data/{trainingData}', [DocumentTrainingController::class, 'deleteTrainingData'])->name('training-data.delete');
// });

// ============================================================================
// MÓDULO DE DASHBOARD DE IA
// ============================================================================

// Route::middleware(['auth', 'can:ai.ver'])->prefix('ai')->name('ai.')->group(function () {
//     
//     // Dashboard principal de IA
//     Route::get('/dashboard', [\App\Http\Controllers\AI\AiDashboardController::class, 'index'])->name('dashboard.index');
//     
//     // Métricas y estadísticas
//     Route::get('/metrics', [\App\Http\Controllers\AI\AiDashboardController::class, 'getMetrics'])->name('metrics');
//     Route::get('/system-health', [\App\Http\Controllers\AI\AiDashboardController::class, 'getSystemHealth'])->name('system-health');
//     Route::get('/training-status', [\App\Http\Controllers\AI\AiDashboardController::class, 'getTrainingStatus'])->name('training-status');
//     
//     // Acciones rápidas
//     Route::post('/quick-action', [\App\Http\Controllers\AI\AiDashboardController::class, 'quickAction'])->name('quick-action');
// });

if (config('app.debug')) {
    
    // Página de índice para probar errores
    Route::get('/test-errors', function() {
        return view('test-errors');
    })->name('test.errors.index');
    
    Route::get('/test-errors/403', function() {
        abort(403);
    })->name('test.error.403');
    
    Route::get('/test-errors/404', function() {
        abort(404);
    })->name('test.error.404');
    
    Route::get('/test-errors/419', function() {
        abort(419);
    })->name('test.error.419');
    
    Route::get('/test-errors/429', function() {
        abort(429);
    })->name('test.error.429');
    
    Route::get('/test-errors/500', function() {
        abort(500);
    })->name('test.error.500');
    
    Route::get('/test-errors/503', function() {
        abort(503);
    })->name('test.error.503');
}

// ============================================================================
// MÓDULO DE NOTIFICACIONES (SOLO API - SIN INTERFAZ)
// ============================================================================

Route::middleware(['auth'])->prefix('notificaciones')->group(function () {
    Route::get('/', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/header', [NotificacionController::class, 'obtenerParaHeader'])->name('notificaciones.header');
    Route::get('/obtener-todas', [NotificacionController::class, 'obtenerTodas'])->name('notificaciones.obtener-todas');
    Route::post('/{id}/marcar-leida', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcar-leida');
    Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas'])->name('notificaciones.marcar-todas-leidas');
    Route::delete('/{id}', [NotificacionController::class, 'eliminar'])->name('notificaciones.eliminar');
    Route::get('/contador', [NotificacionController::class, 'contadorNoLeidas'])->name('notificaciones.contador');
    Route::post('/crear', [NotificacionController::class, 'crear'])->name('notificaciones.crear');
});

// Rutas para documentos
Route::prefix('documentos')->name('documentos.')->middleware(['auth'])->group(function () {
    Route::get('/{documentoSolicitante}', [DocumentoSolicitanteController::class, 'ver'])->name('ver');
    Route::get('/version/{documentoVersion}', [DocumentoSolicitanteController::class, 'verVersion'])->name('ver-version');
});

// Ruta para verificar configuración PHP (solo en desarrollo)
if (config('app.debug')) {
    Route::get('/php-config', function() {
        $config = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'file_uploads' => ini_get('file_uploads') ? 'Habilitado' : 'Deshabilitado',
        ];
        
        return view('php-config', compact('config'));
    })->name('php.config');
}

// ============================================================================
// MÓDULO DE MIS TRÁMITES
// ============================================================================

Route::middleware(['auth', 'can:tramites-solicitante.ver'])->group(function () {
    Route::resource('mis-tramites', MisTramitesController::class)->only([
        'index', 'show', 'edit'
    ]);
    Route::get('/mis-tramites/{tramite}/download', [MisTramitesController::class, 'download'])
        ->name('mis-tramites.download');
});

// Rutas de membretes
Route::get('/membretes/citas/{tramite}/generar', [MembretesController::class, 'generarCita'])
    ->name('membretes.citas.generar')
    ->middleware('auth');

// Ruta para validar citas
Route::get('/citas/validar/{cita}', [App\Http\Controllers\CitaController::class, 'validar'])
    ->name('citas.validar');

// Ruta para ver documentos
Route::get('/documentos/{documentoSolicitante}', [DocumentoController::class, 'verDocumento'])
    ->name('documentos.ver')
    ->middleware('auth');

// Rutas para reagendación de citas
Route::get('/citas/siguiente-dia-disponible/{tramite}', [CitaController::class, 'siguienteDiaDisponible'])
    ->name('citas.siguiente-dia-disponible');
Route::post('/citas/reagendar/{tramite}', [CitaController::class, 'reagendar'])
    ->name('citas.reagendar');

// Rutas para reagendación de citas
Route::get('/citas/{tramite}/reagendar', [CitaController::class, 'reagendar'])->name('citas.reagendar');
Route::get('/citas/{tramite}/reagendada', [CitaController::class, 'mostrarReagendacion'])->name('citas.reagendada');

// Ruta para cancelación de trámites
Route::get('/tramites/{tramite}/cancelar', [TramiteSolicitanteController::class, 'cancelar'])->name('tramites.cancelar');

