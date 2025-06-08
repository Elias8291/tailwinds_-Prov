<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistorialProveedorController extends Controller
{
    public function buscarPorRFC(Request $request)
    {
        try {
            $rfc = $request->input('rfc');
            
            // Buscar todos los registros del proveedor con sus relaciones
            $proveedores = Proveedor::with(['solicitante', 'detallesTramite'])
                ->whereHas('solicitante', function($query) use ($rfc) {
                    $query->where('rfc', $rfc);
                })
                ->orderBy('fecha_registro', 'desc')
                ->get();

            if ($proveedores->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró historial para el RFC proporcionado',
                    'html' => $this->generarHTMLSinResultados()
                ]);
            }

            // Obtener el proveedor más reciente para el resumen
            $proveedorReciente = $proveedores->first();
            $datosProveedor = [
                'rfc' => $proveedorReciente->solicitante->rfc,
                'tipoPersona' => $proveedorReciente->solicitante->tipo_persona,
                'nombre' => $proveedorReciente->solicitante->nombre,
                'razonSocial' => $proveedorReciente->solicitante->razon_social,
                'estado' => $proveedorReciente->estado,
                'pv' => $proveedorReciente->pv
            ];

            // Generar el HTML para el historial
            $html = $this->generarHTMLHistorial($proveedores);

            return response()->json([
                'success' => true,
                'message' => 'Historial encontrado',
                'html' => $html,
                'proveedor' => $datosProveedor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el historial: ' . $e->getMessage(),
                'html' => $this->generarHTMLError()
            ]);
        }
    }

    private function generarHTMLHistorial($proveedores)
    {
        $html = '<ol class="relative border-s-2 border-[#9d2449]/20">';
        
        foreach ($proveedores as $proveedor) {
            $estado = $this->obtenerEstadoFormateado($proveedor->estado);
            $estadoVencimiento = $this->obtenerEstadoVencimiento($proveedor->fecha_vencimiento);
            
            $html .= '
            <li class="mb-10 ms-6">
                <div class="absolute w-4 h-4 ' . $estado['bgColor'] . ' rounded-full mt-1.5 -start-2 border-2 border-white"></div>
                <div class="p-4 bg-white rounded-lg border ' . $estado['border'] . ' shadow-sm hover:shadow-md transition-all duration-300 ' . $estado['hover'] . '">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <time class="text-sm font-normal text-gray-400">
                                Registro: ' . $proveedor->fecha_registro->format('d/m/Y') . '
                            </time>
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full ' . $estado['badge'] . '">
                                ' . $estado['label'] . '
                            </span>
                        </div>
                        <div class="text-sm font-medium ' . $estado['textColor'] . '">
                            PV: ' . e($proveedor->pv) . '
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Información Principal -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                ' . ($proveedor->solicitante->tipo_persona === "Moral" ? 
                                    e($proveedor->solicitante->razon_social) : 
                                    e($proveedor->solicitante->nombre)) . '
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                RFC: ' . e($proveedor->solicitante->rfc) . '
                            </p>
                        </div>

                        <!-- Fechas y Estado -->
                        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Fecha de Vencimiento</p>
                                <p class="text-sm text-gray-600 mt-1">' . ($proveedor->fecha_vencimiento ? $proveedor->fecha_vencimiento->format('d/m/Y') : 'No disponible') . '</p>
                                <p class="text-xs mt-1 ' . $estadoVencimiento['class'] . ' font-medium">
                                    ' . $estadoVencimiento['message'] . '
                                </p>
                            </div>
                            <div class="flex items-end justify-end">
                                ' . ($proveedor->observaciones ? '
                                <button type="button" 
                                        onclick="toggleObservaciones(\'' . e($proveedor->pv) . '\')"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449]">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Ver Observaciones
                                </button>
                                ' : '') . '
                            </div>
                        </div>

                        <!-- Observaciones (ocultas por defecto) -->
                        ' . ($proveedor->observaciones ? '
                        <div id="observaciones-' . e($proveedor->pv) . '" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-600">' . nl2br(e($proveedor->observaciones)) . '</p>
                        </div>
                        ' : '') . '
                    </div>
                </div>
            </li>';
        }

        $html .= '</ol>';

        // Agregar JavaScript para manejar las observaciones
        $html .= '
        <script>
            window.toggleObservaciones = function(pv) {
                const observaciones = document.getElementById(`observaciones-${pv}`);
                if (observaciones) {
                    observaciones.classList.toggle("hidden");
                }
            }
        </script>';

        return $html;
    }

    private function generarHTMLSinResultados()
    {
        return '
        <div class="text-center py-6">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Sin resultados</h3>
            <p class="text-gray-500 text-sm">No se encontró ningún historial para el RFC proporcionado</p>
        </div>';
    }

    private function generarHTMLError()
    {
        return '
        <div class="text-center py-6">
            <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Error</h3>
            <p class="text-gray-500 text-sm">Ocurrió un error al buscar el historial</p>
        </div>';
    }

    private function obtenerEstadoFormateado($estado)
    {
        $estados = [
            'Activo' => [
                'label' => 'Activo',
                'bgColor' => 'bg-green-500',
                'bgLight' => 'bg-green-100',
                'textColor' => 'text-green-700',
                'badge' => 'bg-green-100 text-green-800',
                'border' => 'border-green-200',
                'hover' => 'hover:border-green-300',
                'icon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                          </svg>'
            ],
            'Inactivo' => [
                'label' => 'Inactivo',
                'bgColor' => 'bg-red-500',
                'bgLight' => 'bg-red-100',
                'textColor' => 'text-red-700',
                'badge' => 'bg-red-100 text-red-800',
                'border' => 'border-red-200',
                'hover' => 'hover:border-red-300',
                'icon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>'
            ],
            'Pendiente Renovacion' => [
                'label' => 'Pendiente Renovación',
                'bgColor' => 'bg-yellow-500',
                'bgLight' => 'bg-yellow-100',
                'textColor' => 'text-yellow-700',
                'badge' => 'bg-yellow-100 text-yellow-800',
                'border' => 'border-yellow-200',
                'hover' => 'hover:border-yellow-300',
                'icon' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>'
            ]
        ];

        return $estados[$estado] ?? $estados['Inactivo'];
    }

    private function obtenerEstadoVencimiento($fechaVencimiento)
    {
        if (!$fechaVencimiento) {
            return [
                'message' => 'Fecha de vencimiento no disponible',
                'class' => 'text-gray-500'
            ];
        }

        $now = Carbon::now();
        $fechaVencimiento = Carbon::parse($fechaVencimiento);
        
        if ($fechaVencimiento->isFuture()) {
            // Calcular diferencia para proveedores activos
            $diff = $now->diff($fechaVencimiento);
            $meses = $diff->m + ($diff->y * 12);
            $dias = $diff->d;
            $horas = $diff->h;
            
            $mensaje = "Tiempo restante: ";
            if ($meses > 0) $mensaje .= $meses . " mes(es), ";
            if ($dias > 0) $mensaje .= $dias . " día(s), ";
            $mensaje .= $horas . " hora(s)";
            
            return [
                'class' => 'text-green-600',
                'message' => $mensaje
            ];
        } else if ($fechaVencimiento->isPast()) {
            // Calcular tiempo transcurrido desde el vencimiento
            $diff = $fechaVencimiento->diff($now);
            $meses = $diff->m + ($diff->y * 12);
            $dias = $diff->d;
            $horas = $diff->h;
            
            $mensaje = "Vencido hace: ";
            if ($meses > 0) $mensaje .= $meses . " mes(es), ";
            if ($dias > 0) $mensaje .= $dias . " día(s), ";
            $mensaje .= $horas . " hora(s)";
            
            return [
                'class' => 'text-red-600',
                'message' => $mensaje
            ];
        } else {
            return [
                'class' => 'text-yellow-600',
                'message' => 'Vence hoy'
            ];
        }
    }
} 