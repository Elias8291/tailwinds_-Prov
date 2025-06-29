<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tramite;
use App\Models\Proveedor;
use App\Models\Solicitante;

class ActualizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar selector de sección para actualizar
     */
    public function mostrarSelector()
    {
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No se encontró información del solicitante');
        }

        // Buscar proveedor activo
        $proveedor = Proveedor::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Activo')
            ->first();
            
        if (!$proveedor) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No tiene un registro de proveedor activo');
        }

        // Buscar el trámite aprobado que lo hizo proveedor
        $tramiteAprobado = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Aprobado')
            ->orderBy('fecha_revision', 'desc')
            ->first();
            
        if (!$tramiteAprobado) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No se encontró el trámite base');
        }

        $datosActuales = $this->obtenerDatosActuales($tramiteAprobado);

        return view('tramites.actualizacion.selector', compact(
            'proveedor', 
            'tramiteAprobado', 
            'datosActuales'
        ));
    }

    /**
     * Iniciar actualización de sección específica
     */
    public function iniciarSeccion(Request $request, $seccionId)
    {
        $user = Auth::user();
        $tramiteBase = Tramite::find($request->tramite_base_id);
        
        if ($tramiteBase->solicitante->usuario_id !== $user->id) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No tiene permisos');
        }

        // Crear trámite de actualización con datos pre-cargados
        $tramiteActualizacion = Tramite::create([
            'solicitante_id' => $tramiteBase->solicitante_id,
            'tipo_tramite' => 'Actualizacion',
            'estado' => 'Pendiente',
            'fecha_inicio' => now(),
            'tramite_padre_id' => $tramiteBase->id,
            'seccion_actualizar' => $seccionId
        ]);

        // Pre-cargar datos existentes
        $this->precargarDatos($tramiteBase, $tramiteActualizacion);

        return redirect()->route('tramites.create.tipo', [
            'tipo_tramite' => 'actualizacion',
            'tramite' => $tramiteActualizacion->id
        ])->with('success', 'Datos cargados. Modifique solo lo necesario.');
    }

    private function precargarDatos($tramiteBase, $tramiteNuevo)
    {
        // Copiar DetalleTramite
        if ($tramiteBase->detalleTramite) {
            $detalle = $tramiteBase->detalleTramite->replicate();
            $detalle->tramite_id = $tramiteNuevo->id;
            $detalle->save();
        }
        
        // Copiar otros datos según necesidad...
    }

    private function obtenerDatosActuales($tramite)
    {
        return [
            'datos_generales' => [
                'razon_social' => $tramite->solicitante->razon_social ?? 'N/A',
                'giro' => $tramite->detalleTramite->giro ?? 'N/A'
            ],
            'domicilio' => [
                'estado_nombre' => $tramite->direccion->estado->nombre ?? 'N/A',
                'municipio_nombre' => $tramite->direccion->municipio->nombre ?? 'N/A'
            ]
        ];
    }
} 