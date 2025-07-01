<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'citas';

    protected $fillable = [
        'user_id',
        'tramite_id',
        'fecha_hora',
        'motivo',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    /**
     * Agendar cita automáticamente para cotejo de documentos
     * Busca el próximo horario disponible del día siguiente
     * Horario: 9:00-15:00, máximo 4 personas por hora
     */
    public static function agendarCotejoAutomatico($tramite)
    {
        try {
            $fechaInicio = Carbon::tomorrow()->setTime(9, 0); // Día siguiente a las 9:00
            $fechaFin = Carbon::tomorrow()->setTime(15, 0);   // Hasta las 15:00
            
            // Buscar primera hora disponible
            $horarioSeleccionado = null;
            $currentHour = $fechaInicio->copy();
            
            while ($currentHour->lessThan($fechaFin)) {
                // Contar citas existentes en esta hora
                $citasExistentes = self::where('fecha_hora', $currentHour)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->count();
                
                if ($citasExistentes < 4) { // Máximo 4 personas por hora
                    $horarioSeleccionado = $currentHour->copy();
                    break;
                }
                
                $currentHour->addHour();
            }
            
            if (!$horarioSeleccionado) {
                // Si no hay horarios disponibles mañana, buscar en los próximos días laborables
                $horarioSeleccionado = self::buscarProximoHorarioDisponible();
            }
            
            if (!$horarioSeleccionado) {
                throw new \Exception('No hay horarios disponibles para agendar la cita');
            }
            
            // Crear la cita
            $cita = self::create([
                'user_id' => $tramite->solicitante->usuario_id ?? null,
                'tramite_id' => $tramite->id,
                'fecha_hora' => $horarioSeleccionado,
                'motivo' => 'Cotejo físico de documentos - Trámite ' . $tramite->tipo_tramite,
                'estado' => 'confirmada',
                'notas' => 'Cita agendada automáticamente después de completar la revisión digital. ' .
                          'Por favor traiga todos los documentos originales para su cotejo.'
            ]);
            
            Log::info('Cita de cotejo agendada automáticamente:', [
                'cita_id' => $cita->id,
                'tramite_id' => $tramite->id,
                'fecha_hora' => $horarioSeleccionado->format('Y-m-d H:i:s'),
                'user_id' => $tramite->solicitante->usuario_id ?? null
            ]);
            
            return $cita;
            
        } catch (\Exception $e) {
            Log::error('Error al agendar cita automática:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Buscar próximo horario disponible en días laborables
     */
    private static function buscarProximoHorarioDisponible()
    {
        $diasABuscar = 14; // Buscar hasta 2 semanas adelante
        
        for ($dia = 1; $dia <= $diasABuscar; $dia++) {
            $fecha = Carbon::now()->addDays($dia);
            
            // Saltar fines de semana
            if ($fecha->isWeekend()) {
                continue;
            }
            
            // Buscar horarios de 9:00 a 15:00
            for ($hora = 9; $hora < 15; $hora++) {
                $horario = $fecha->copy()->setTime($hora, 0);
                
                $citasExistentes = self::where('fecha_hora', $horario)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->count();
                
                if ($citasExistentes < 4) {
                    return $horario;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Completar cita y permitir creación de proveedor
     */
    public function completarCotejo($exitoso = true, $observaciones = null)
    {
        $this->update([
            'estado' => 'completada',
            'notas' => $this->notas . "\n\nCotejo completado el " . now()->format('d/m/Y H:i') . 
                      ($exitoso ? ' - EXITOSO' : ' - FALLIDO') . 
                      ($observaciones ? "\nObservaciones: " . $observaciones : '')
        ]);
        
        // Si el cotejo fue exitoso, permitir creación del proveedor
        if ($exitoso && $this->tramite) {
            return $this->tramite;
        }
        
        return null;
    }
} 