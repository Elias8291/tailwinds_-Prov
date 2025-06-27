<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificacion';

    protected $fillable = [
        'titulo',
        'mensaje',
        'fecha',
        'tipo',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    /**
     * Relación muchos a muchos con usuarios
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'notificacion_usuario', 'notificacion_id', 'usuario_id')
                    ->withPivot('fecha_notificacion', 'estado')
                    ->withTimestamps();
    }

    /**
     * Obtener notificaciones para un usuario específico
     */
    public static function paraUsuario($usuarioId, $limite = 10, $soloNoLeidas = false)
    {
        $query = static::whereHas('usuarios', function ($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId)
              ->where('notificacion_usuario.estado', '!=', 'Eliminado'); // Excluir eliminadas
        })
        ->with(['usuarios' => function ($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId);
        }])
        ->orderBy('fecha', 'desc');

        if ($soloNoLeidas) {
            $query->whereHas('usuarios', function ($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId)
                  ->where('notificacion_usuario.estado', 'Pendiente');
            });
        }

        return $query->limit($limite)->get();
    }

    /**
     * Contar notificaciones no leídas de un usuario
     */
    public static function contarNoLeidas($usuarioId)
    {
        return static::whereHas('usuarios', function ($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId)
              ->where('notificacion_usuario.estado', 'Pendiente');
        })->count();
    }

    /**
     * Marcar como leída para un usuario específico
     */
    public function marcarComoLeida($usuarioId)
    {
        return $this->usuarios()->updateExistingPivot($usuarioId, [
            'estado' => 'Leido'
        ]);
    }

    /**
     * Crear notificación para usuario específico
     */
    public static function crearParaUsuario($titulo, $mensaje, $tipo, $usuarioId)
    {
        $notificacion = static::create([
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'fecha' => now(),
            'tipo' => $tipo,
            'estado' => 'Pendiente'
        ]);

        $notificacion->usuarios()->attach($usuarioId, [
            'fecha_notificacion' => now(),
            'estado' => 'Pendiente'
        ]);

        return $notificacion;
    }

    /**
     * Crear notificación para múltiples usuarios
     */
    public static function crearParaUsuarios($titulo, $mensaje, $tipo, $usuariosIds)
    {
        $notificacion = static::create([
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'fecha' => now(),
            'tipo' => $tipo,
            'estado' => 'Pendiente'
        ]);

        $datosUsuarios = [];
        foreach ($usuariosIds as $usuarioId) {
            $datosUsuarios[$usuarioId] = [
                'fecha_notificacion' => now(),
                'estado' => 'Pendiente'
            ];
        }

        $notificacion->usuarios()->attach($datosUsuarios);

        return $notificacion;
    }

    /**
     * Obtener el ícono según el tipo
     */
    public function getIconoAttribute()
    {
        return match($this->tipo) {
            'Informativo' => 'fas fa-info-circle',
            'Advertencia' => 'fas fa-exclamation-triangle', 
            'Error' => 'fas fa-times-circle',
            default => 'fas fa-bell'
        };
    }

    /**
     * Obtener el color según el tipo
     */
    public function getColorAttribute()
    {
        return match($this->tipo) {
            'Informativo' => 'blue',
            'Advertencia' => 'yellow',
            'Error' => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener tiempo transcurrido
     */
    public function getTiempoTranscurridoAttribute()
    {
        return $this->fecha->diffForHumans();
    }
} 