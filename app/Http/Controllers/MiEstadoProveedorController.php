<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Tramite;
use App\Models\Solicitante;
use Carbon\Carbon;

class MiEstadoProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Comentamos temporalmente el middleware de permisos para testing
        // $this->middleware('permission:mi-estado-proveedor.ver');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Verificar si el usuario tiene el permiso manualmente
        if (!$user->can('mi-estado-proveedor.ver')) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }
        
        // Buscar el proveedor asociado al usuario actual a través del solicitante
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        $proveedor = $solicitante ? $solicitante->proveedor : null;
        
        if (!$proveedor) {
            return view('mi-estado-proveedor.index', [
                'proveedor' => null,
                'estadoInscripcion' => 'Sin inscripción',
                'fechaVencimiento' => null,
                'diasRestantes' => null,
                'tramitesActivos' => collect(),
                'alertas' => []
            ]);
        }

        // Obtener trámites activos del proveedor
        $tramitesActivos = Tramite::where('proveedor_id', $proveedor->id)
            ->whereIn('estado', ['en_proceso', 'en_revision', 'pendiente'])
            ->with(['solicitante', 'detalleTramite'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Calcular estado de inscripción
        $fechaVencimiento = $proveedor->fecha_vencimiento;
        $estadoInscripcion = 'Activa';
        $diasRestantes = null;
        $alertas = [];

        if ($fechaVencimiento) {
            $fechaVencimiento = Carbon::parse($fechaVencimiento);
            $hoy = Carbon::now();
            $diasRestantes = $hoy->diffInDays($fechaVencimiento, false);

            if ($diasRestantes < 0) {
                $estadoInscripcion = 'Vencida';
                $alertas[] = [
                    'tipo' => 'danger',
                    'mensaje' => 'Su inscripción venció hace ' . abs($diasRestantes) . ' días. Debe renovar urgentemente.'
                ];
            } elseif ($diasRestantes <= 30) {
                $estadoInscripcion = 'Por vencer';
                $alertas[] = [
                    'tipo' => 'warning',
                    'mensaje' => 'Su inscripción vence en ' . $diasRestantes . ' días. Considere renovar pronto.'
                ];
            } elseif ($diasRestantes <= 60) {
                $alertas[] = [
                    'tipo' => 'info',
                    'mensaje' => 'Su inscripción vence en ' . $diasRestantes . ' días.'
                ];
            }
        } else {
            $estadoInscripcion = 'Sin fecha definida';
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => 'No se ha definido una fecha de vencimiento para su inscripción.'
            ];
        }

        // Verificar si hay documentos pendientes a través de los trámites
        $documentosPendientes = 0;
        if ($solicitante) {
            $documentosPendientes = $solicitante->documentosSolicitante()
                ->where('estado', 'pendiente')
                ->count();
        }
        
        if ($documentosPendientes > 0) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => "Tiene {$documentosPendientes} documento(s) pendiente(s) de revisión."
            ];
        }

        return view('mi-estado-proveedor.index', compact(
            'proveedor',
            'estadoInscripcion',
            'fechaVencimiento',
            'diasRestantes',
            'tramitesActivos',
            'alertas'
        ));
    }
} 