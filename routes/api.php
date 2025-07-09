<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfcSearchController;
use App\Http\Controllers\HistorialProveedorController;
use App\Http\Controllers\Api\SectorController;
use App\Http\Controllers\LocationDataController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\Formularios\DocumentosController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// RFC Search Routes
Route::get('/rfc-search/{rfc}', [RfcSearchController::class, 'search']);
Route::get('/rfc-history/{rfc}', [RfcSearchController::class, 'history']);
Route::get('/proveedor/historial', [HistorialProveedorController::class, 'buscarPorRFC']);

// Actividades Routes (orden específico a general)
Route::get('/actividades/buscar', [SectorController::class, 'buscarActividades']);
Route::post('/actividades/obtener-por-ids', [SectorController::class, 'obtenerPorIds']);
Route::get('/actividades/{actividad}', [SectorController::class, 'getActividad']);
Route::get('/actividades', [SectorController::class, 'getAllActividades']);

// Sector Routes
Route::get('/sectores/{sector}/actividades', [SectorController::class, 'getActividades']);

// Location Data Routes
Route::get('/location-data/{codigoPostal}', [LocationDataController::class, 'getLocationData']);

// Tramite Routes
Route::get('/tramite/{tramiteId}/domicilio', [App\Http\Controllers\DetalleTramiteController::class, 'getDomicilioApi']);
Route::get('/tramite/{tramiteId}/constitucion', [App\Http\Controllers\DetalleTramiteController::class, 'getConstitucionApi']);

// Documentos Routes
Route::post('/documentos/{id}/aprobar', [DocumentosController::class, 'aprobar']);
Route::post('/documentos/{id}/rechazar', [DocumentosController::class, 'rechazar']);