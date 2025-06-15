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
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\API\SectorController;
use App\Http\Controllers\TramiteNavegacionController;
use App\Http\Controllers\Formularios\DocumentosController;

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
    Route::get('/revision/{tramite}/datos-generales', [\App\Http\Controllers\DatosGeneralesController::class, 'prepararDatosRevision'])->name('revision.datos-generales');
});

// Rutas para trámites
Route::prefix('tramites')->group(function () {
    Route::get('/', [TramiteController::class, 'index'])->name('tramites.index');
    Route::post('/iniciar', [TramiteController::class, 'iniciarTramite'])->name('tramites.iniciar');
    Route::get('/datos-generales', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'index'])->name('tramites.datos-generales');
    Route::post('/guardar-datos-generales', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'guardar'])->name('tramites.guardar-datos-generales');
    
    Route::get('/domicilio', [TramiteController::class, 'mostrarDomicilio'])->name('tramites.domicilio');
    Route::post('/guardar-domicilio', [TramiteController::class, 'guardarDomicilio'])->name('tramites.guardar-domicilio');
    Route::post('/guardar-domicilio-formulario', [DireccionController::class, 'guardarFormulario'])->name('tramites.guardar-domicilio-formulario');
    Route::post('/guardar-constitucion-formulario', [ConstitucionController::class, 'guardarFormulario'])->name('tramites.guardar-constitucion-formulario');
    Route::post('/guardar-accionistas-formulario', [\App\Http\Controllers\Formularios\AccionistasController::class, 'guardarFormulario'])->name('tramites.guardar-accionistas-formulario');
    Route::post('/guardar-apoderado-formulario', [\App\Http\Controllers\Formularios\ApoderadoLegalController::class, 'guardarFormulario'])->name('tramites.guardar-apoderado-formulario');
    
    // Rutas específicas para cada tipo de trámite
    Route::get('/{tipo_tramite}/{tramite}/create', [TramiteController::class, 'create'])
        ->where('tipo_tramite', 'inscripcion|renovacion|actualizacion')
        ->name('tramites.create');
    
    // Ruta para pasos específicos del trámite
    Route::get('/{tramite}/paso/{paso}', [TramiteNavegacionController::class, 'mostrarPaso'])
        ->where(['tramite' => '[0-9]+', 'paso' => '[0-9]+'])
        ->name('tramites.solicitante.paso');
    
    // Rutas de navegación entre pasos
    Route::match(['get', 'post'], '/{tramite}/siguiente/{paso}', [TramiteNavegacionController::class, 'siguientePaso'])
        ->where(['tramite' => '[0-9]+', 'paso' => '[0-9]+'])
        ->name('tramites.navegacion.siguiente');
    
    Route::get('/{tramite}/anterior/{paso}', [TramiteNavegacionController::class, 'pasoAnterior'])
        ->where(['tramite' => '[0-9]+', 'paso' => '[0-9]+'])
        ->name('tramites.navegacion.anterior');
    
    Route::post('/store', [TramiteController::class, 'store'])->name('tramites.store');
});

// Rutas para el formulario de datos generales
Route::middleware(['auth'])->prefix('formularios')->group(function () {
    Route::post('/datos-generales/guardar', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'guardar'])->name('datos-generales.guardar');
    
    // Ruta de prueba para el componente
    Route::get('/datos-generales/test/{tramite?}', function($tramiteId = null) {
        $datosTramite = [];
        $datosSolicitante = [];
        
        if ($tramiteId) {
            $tramite = \App\Models\Tramite::findOrFail($tramiteId);
            $controller = new \App\Http\Controllers\Formularios\DatosGeneralesController();
            $datosTramite = $controller->obtenerDatos($tramite);
            
            $solicitante = $tramite->solicitante;
            if ($solicitante) {
                $datosSolicitante = [
                    'rfc' => $solicitante->rfc,
                    'tipo_persona' => $solicitante->tipo_persona,
                    'curp' => $solicitante->curp,
                    'razon_social' => $solicitante->razon_social,
                ];
            }
        }
        
        return view('tramites.datos-generales-test', compact('datosTramite', 'datosSolicitante'));
    })->name('datos-generales.test');
    
    // Ruta de prueba para navegación de pasos
    Route::get('/navegacion/test/{tramite?}', function($tramiteId = null) {
        if (!$tramiteId) {
            // Si no hay trámite, crear uno de prueba o listar trámites disponibles
            $tramites = \App\Models\Tramite::with('solicitante')->take(10)->get();
            return view('tramites.navegacion-test', compact('tramites'));
        }
        
        // Redirigir al primer paso del trámite
        return redirect()->route('tramites.solicitante.paso', [
            'tramite' => $tramiteId,
            'paso' => 1
        ]);
    })->name('navegacion.test');
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
    
    // Rutas para constancia de situación fiscal
    Route::get('/constancia-fiscal/{tipo_tramite}/{tramite}', [TramiteSolicitanteController::class, 'mostrarConstanciaFiscal'])->name('tramites.solicitante.constancia-fiscal');
    Route::post('/constancia-fiscal/subir', [TramiteSolicitanteController::class, 'subirConstanciaFiscal'])->name('tramites.solicitante.subir-constancia-fiscal');
    
    // Nuevas rutas para obtener datos dinámicamente
    Route::get('/datos-tramite', [TramiteSolicitanteController::class, 'obtenerDatosTramite'])->name('tramites.solicitante.datos-tramite');
    Route::get('/documentos', [TramiteSolicitanteController::class, 'obtenerDocumentos'])->name('tramites.solicitante.documentos');
    Route::post('/upload-documento', [\App\Http\Controllers\Formularios\DocumentosController::class, 'subir'])->name('tramites.solicitante.upload-documento');
    Route::post('/finalizar', [TramiteSolicitanteController::class, 'finalizarTramite'])->name('tramites.solicitante.finalizar');
    
    // API para obtener datos de domicilio
    Route::get('/api/domicilio/{tramite}', [TramiteSolicitanteController::class, 'obtenerDatosDomicilioAPI'])->name('tramites.solicitante.api.domicilio');
});

// Rutas de API para sectores y actividades
Route::prefix('api')->group(function () {
    Route::get('/sectores/{sector}/actividades', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'getActividadesPorSector']);
    Route::get('/actividades', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'getAllActividades']);
    Route::get('/actividades/{actividad}', [SectorController::class, 'getActividad']);
    
    // Rutas para el controlador de datos generales
    Route::get('/datos-generales/{tramite}', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'obtenerDatos'])->name('api.datos-generales.obtener');
});

// Ruta temporal para debugging - ELIMINAR EN PRODUCCIÓN
Route::get('/debug-tramite/{tramite}', function($tramiteId) {
    $tramite = \App\Models\Tramite::find($tramiteId);
    
    if (!$tramite) {
        return response()->json(['error' => 'Trámite no encontrado']);
    }
    
    $solicitante = $tramite->solicitante;
    $detalleTramite = $tramite->detalleTramite;
    $contacto = $detalleTramite ? $detalleTramite->contacto : null;
    $actividades = $tramite->actividades;
    
    // También buscar ContactoSolicitante directamente si existe contacto_id
    $contactoDirecto = null;
    if ($detalleTramite && $detalleTramite->contacto_id) {
        $contactoDirecto = \App\Models\ContactoSolicitante::find($detalleTramite->contacto_id);
    }
    
    return response()->json([
        'tramite' => [
            'id' => $tramite->id,
            'tipo_tramite' => $tramite->tipo_tramite,
            'estado' => $tramite->estado,
            'solicitante_id' => $tramite->solicitante_id
        ],
        'solicitante' => $solicitante ? [
            'id' => $solicitante->id,
            'rfc' => $solicitante->rfc,
            'tipo_persona' => $solicitante->tipo_persona,
            'objeto_social' => $solicitante->objeto_social,
            'razon_social' => $solicitante->razon_social ?? 'NULL',
            'nombre_completo' => $solicitante->nombre_completo ?? 'NULL'
        ] : null,
        'detalle_tramite' => $detalleTramite ? [
            'id' => $detalleTramite->id,
            'tramite_id' => $detalleTramite->tramite_id,
            'razon_social' => $detalleTramite->razon_social,
            'email' => $detalleTramite->email,
            'telefono' => $detalleTramite->telefono,
            'contacto_id' => $detalleTramite->contacto_id,
            'sitio_web' => $detalleTramite->sitio_web
        ] : null,
        'contacto_relacion' => $contacto ? [
            'id' => $contacto->id,
            'nombre' => $contacto->nombre,
            'puesto' => $contacto->puesto,
            'email' => $contacto->email,
            'telefono' => $contacto->telefono
        ] : null,
        'contacto_directo' => $contactoDirecto ? [
            'id' => $contactoDirecto->id,
            'nombre' => $contactoDirecto->nombre,
            'puesto' => $contactoDirecto->puesto,
            'email' => $contactoDirecto->email,
            'telefono' => $contactoDirecto->telefono
        ] : null,
        'actividades' => $actividades->map(function($act) {
            return [
                'id' => $act->id,
                'nombre' => $act->nombre,
                'sector_id' => $act->sector_id
            ];
        }),
        'actividades_count' => $actividades->count()
    ]);
});

// Ruta temporal para testing obtenerDatos - ELIMINAR EN PRODUCCIÓN
Route::get('/test-obtener-datos/{tramite?}', [\App\Http\Controllers\Formularios\DatosGeneralesController::class, 'testObtenerDatos']);

// Ruta temporal para probar el formulario con trámite específico - ELIMINAR EN PRODUCCIÓN
Route::get('/test-form-tramite/{tramite}', function($tramiteId) {
    return redirect()->route('tramites.datos-generales', ['tramite_id' => $tramiteId]);
});

// Ruta para debuggear problemas de sesión - ELIMINAR EN PRODUCCIÓN
Route::get('/test-session-debug', function () {
    return response()->json([
        'session_started' => session()->isStarted(),
        'session_id' => session()->getId(),
        'user_authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'user_data' => Auth::user() ? [
            'id' => Auth::user()->id,
            'nombre' => Auth::user()->nombre,
            'has_solicitante' => Auth::user()->solicitante !== null,
            'solicitante_id' => Auth::user()->solicitante?->id
        ] : null,
        'session_data_keys' => array_keys(session()->all()),
        'csrf_token' => csrf_token(),
        'request_info' => [
            'method' => request()->method(),
            'url' => request()->url(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]
    ]);
})->middleware('auth')->name('test.session-debug');
