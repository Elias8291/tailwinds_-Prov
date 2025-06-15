<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfcSearchController;
use App\Http\Controllers\HistorialProveedorController;
use App\Http\Controllers\API\SectorController;
use App\Http\Controllers\LocationDataController;

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

// Sector Routes
Route::get('/sectores/{sector}/actividades', [SectorController::class, 'getActividades']);
Route::get('/actividades', [SectorController::class, 'getAllActividades']);
Route::get('/actividades/{actividad}', [SectorController::class, 'getActividad']);

Route::get('/location-data/{codigoPostal}', [LocationDataController::class, 'getLocationData']);

// Tramite Routes
Route::get('/tramite/{tramiteId}/domicilio', [App\Http\Controllers\DetalleTramiteController::class, 'getDomicilioApi']);
Route::get('/tramite/{tramiteId}/constitucion', [App\Http\Controllers\DetalleTramiteController::class, 'getConstitucionApi']);
Route::get('/tramite/{tramiteId}/accionistas', [App\Http\Controllers\DetalleTramiteController::class, 'getAccionistasApi']);
Route::get('/tramite/{tramiteId}/apoderado', [App\Http\Controllers\DetalleTramiteController::class, 'getApoderadoApi']);