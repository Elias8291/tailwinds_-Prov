<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    /**
     * Obtener todas las notificaciones del usuario autenticado
     */
    public function index()
    {
        $user = Auth::user();
        $notificaciones = Notificacion::paraUsuario($user->id, 50);
        
        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Obtener notificaciones para el header (AJAX)
     */
    public function obtenerParaHeader()
    {
        $user = Auth::user();
        
        // Obtener últimas 10 notificaciones
        $notificaciones = Notificacion::paraUsuario($user->id, 10);
        
        // Contar no leídas
        $noLeidas = Notificacion::contarNoLeidas($user->id);
        
        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones->map(function ($notificacion) use ($user) {
                $pivotData = $notificacion->usuarios->where('id', $user->id)->first()->pivot ?? null;
                
                return [
                    'id' => $notificacion->id,
                    'titulo' => $notificacion->titulo,
                    'mensaje' => $notificacion->mensaje,
                    'tipo' => $notificacion->tipo,
                    'icono' => $notificacion->icono,
                    'color' => $notificacion->color,
                    'fecha' => $notificacion->fecha->format('d/m/Y H:i'),
                    'tiempo_transcurrido' => $notificacion->tiempo_transcurrido,
                    'leida' => $pivotData ? $pivotData->estado === 'Leido' : false
                ];
            }),
            'contador_no_leidas' => $noLeidas
        ]);
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida(Request $request, $id)
    {
        $user = Auth::user();
        $notificacion = Notificacion::findOrFail($id);
        
        $notificacion->marcarComoLeida($user->id);
        
        return response()->json([
            'success' => true,
            'mensaje' => 'Notificación marcada como leída'
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas()
    {
        $user = Auth::user();
        
        // Obtener todas las notificaciones no leídas del usuario
        $notificaciones = Notificacion::whereHas('usuarios', function ($q) use ($user) {
            $q->where('usuario_id', $user->id)
              ->where('notificacion_usuario.estado', 'Pendiente');
        })->get();
        
        // Marcar cada una como leída
        foreach ($notificaciones as $notificacion) {
            $notificacion->marcarComoLeida($user->id);
        }
        
        return response()->json([
            'success' => true,
            'mensaje' => 'Todas las notificaciones han sido marcadas como leídas'
        ]);
    }

    /**
     * Eliminar una notificación (solo para el usuario actual)
     */
    public function eliminar($id)
    {
        $user = Auth::user();
        $notificacion = Notificacion::findOrFail($id);
        
        // Marcar como eliminada en la tabla pivot
        $notificacion->usuarios()->updateExistingPivot($user->id, [
            'estado' => 'Eliminado'
        ]);
        
        return response()->json([
            'success' => true,
            'mensaje' => 'Notificación eliminada'
        ]);
    }

    /**
     * Crear notificación (para administradores)
     */
    public function crear(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'tipo' => 'required|in:Informativo,Advertencia,Error',
            'usuarios' => 'required|array',
            'usuarios.*' => 'exists:users,id'
        ]);

        $notificacion = Notificacion::crearParaUsuarios(
            $request->titulo,
            $request->mensaje,
            $request->tipo,
            $request->usuarios
        );

        return response()->json([
            'success' => true,
            'mensaje' => 'Notificación creada exitosamente',
            'notificacion' => $notificacion
        ]);
    }

    /**
     * Obtener el contador de notificaciones no leídas
     */
    public function contadorNoLeidas()
    {
        $user = Auth::user();
        $contador = Notificacion::contarNoLeidas($user->id);
        
        return response()->json([
            'success' => true,
            'contador' => $contador
        ]);
    }
} 