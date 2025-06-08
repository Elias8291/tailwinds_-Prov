<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfcSearchController;
use App\Http\Controllers\HistorialProveedorController;

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