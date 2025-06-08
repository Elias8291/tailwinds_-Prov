<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\Solicitante;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RfcSearchController extends Controller
{
    public function search($rfc)
    {
        try {
            $proveedores = Proveedor::with(['solicitante', 'detallesTramite'])
                ->whereHas('solicitante', function($query) use ($rfc) {
                    $query->where('rfc', 'LIKE', '%' . $rfc . '%');
                })
                ->get();

            if ($proveedores->isEmpty()) {
                return response()->json([
                    'found' => false,
                    'message' => 'No se encontró historial para el RFC proporcionado',
                    'tramites' => [
                        [
                            'tipo' => 'inscripcion',
                            'nombre' => 'Inscripción al Padrón',
                            'descripcion' => 'Registro inicial como proveedor',
                            'disponible' => true
                        ]
                    ]
                ]);
            }

            // Obtener el proveedor más reciente
            $proveedorActual = $proveedores->sortByDesc('fecha_registro')->first();
            $tramites = $this->determinarTramitesDisponibles($proveedorActual);

            return response()->json([
                'found' => true,
                'message' => 'RFC encontrado',
                'tramites' => $tramites
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al buscar el RFC',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function determinarTramitesDisponibles($proveedor)
    {
        $tramites = [];
        $now = Carbon::now();
        $fechaVencimiento = Carbon::parse($proveedor->fecha_vencimiento);
        $diasParaVencer = $now->diffInDays($fechaVencimiento, false);

        // Trámite de inscripción
        $tramites[] = [
            'tipo' => 'inscripcion',
            'nombre' => 'Inscripción al Padrón',
            'descripcion' => 'Registro inicial como proveedor',
            'disponible' => $proveedor->estado === 'Inactivo' || !$proveedor->estado
        ];

        // Trámite de actualización
        $tramites[] = [
            'tipo' => 'actualizacion',
            'nombre' => 'Actualización de Datos',
            'descripcion' => 'Actualizar información del proveedor',
            'disponible' => $proveedor->estado === 'Activo' && $diasParaVencer > 7
        ];

        // Trámite de renovación
        $tramites[] = [
            'tipo' => 'renovacion',
            'nombre' => 'Renovación de Registro',
            'descripcion' => 'Renovar registro como proveedor',
            'disponible' => $proveedor->estado === 'Activo' && $diasParaVencer <= 7 && $diasParaVencer > 0
        ];

        return $tramites;
    }

    public function history($rfc)
    {
        try {
            $proveedores = Proveedor::with(['solicitante', 'detallesTramite'])
                ->whereHas('solicitante', function($query) use ($rfc) {
                    $query->where('rfc', 'LIKE', '%' . $rfc . '%');
                })
                ->orderBy('fecha_registro', 'desc')
                ->get();

            if ($proveedores->isEmpty()) {
                return response()->json([
                    'found' => false,
                    'message' => 'No se encontró historial para el RFC proporcionado'
                ]);
            }

            $historyData = $proveedores->map(function($proveedor) {
                $title = 'Registro como Proveedor';
                if ($proveedor->estado === 'Renovacion') {
                    $title = 'Renovación de Registro';
                } else if ($proveedor->estado === 'Inactivo') {
                    $title = 'Registro Inactivo';
                }

                // Calcular días para vencer
                $diasParaVencer = $proveedor->dias_para_vencer;
                $mensajeDias = '';
                
                if ($diasParaVencer !== null) {
                    if ($diasParaVencer > 0) {
                        $mensajeDias = "Faltan {$diasParaVencer} días";
                    } else if ($diasParaVencer < 0) {
                        $mensajeDias = "Vencido hace " . abs($diasParaVencer) . " días";
                    } else {
                        $mensajeDias = "Vence hoy";
                    }
                }

                return [
                    'date' => $proveedor->fecha_registro,
                    'expiration_date' => $proveedor->fecha_vencimiento,
                    'title' => $title,
                    'description' => $proveedor->detallesTramite->first() 
                        ? ($proveedor->solicitante->tipo_persona === 'Moral' 
                            ? $proveedor->detallesTramite->first()->razon_social 
                            : $proveedor->solicitante->nombre)
                        : 'Sin detalles adicionales',
                    'status' => $proveedor->estado,
                    'pv' => $proveedor->pv,
                    'dias_mensaje' => $mensajeDias
                ];
            });

            return response()->json([
                'found' => true,
                'history' => $historyData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el historial',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 