<?php

namespace App\Http\Controllers;

use App\Models\Asentamiento;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Localidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationDataController extends Controller
{
    public function getLocationData($codigoPostal)
    {
        try {
            // Get the first asentamiento for this código postal to get the localidad_id
            $asentamiento = Asentamiento::where('codigo_postal', $codigoPostal)
                ->with(['localidad.municipio.estado'])
                ->first();

            if (!$asentamiento) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información para el código postal proporcionado'
                ], 404);
            }

            // Get all asentamientos for this código postal
            $asentamientos = Asentamiento::where('codigo_postal', $codigoPostal)
                ->with('tipoAsentamiento')
                ->get()
                ->map(function ($asentamiento) {
                    return [
                        'id' => $asentamiento->id,
                        'nombre' => $asentamiento->nombre . ' (' . $asentamiento->tipoAsentamiento->nombre . ')'
                    ];
                });

            return response()->json([
                'success' => true,
                'estado' => $asentamiento->localidad->municipio->estado->nombre,
                'municipio' => $asentamiento->localidad->municipio->nombre,
                'asentamientos' => $asentamientos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la información de ubicación'
            ], 500);
        }
    }
} 