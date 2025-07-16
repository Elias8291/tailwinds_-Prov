<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfcSearchController;
use App\Http\Controllers\HistorialProveedorController;
use App\Http\Controllers\Api\SectorController;
use App\Http\Controllers\LocationDataController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\Formularios\DocumentosController;
use App\Http\Controllers\Api\GoogleMapsProxyController;
use App\Http\Controllers\NotificacionController;

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

// Validation Routes
Route::controller(\App\Http\Controllers\UserController::class)->group(function () {
    Route::get('/validate/email', 'validateEmail');
    Route::get('/validate/rfc', 'validateRfc');
});

Route::middleware('auth:sanctum')->prefix('notificaciones')->controller(NotificacionController::class)->group(function () {
    Route::get('/header', 'header');
    Route::get('/contador', 'contador');
    Route::post('/marcar-todas-leidas', 'marcarTodasLeidas');
    Route::post('/{id}/marcar-leida', 'marcarComoLeida');
});