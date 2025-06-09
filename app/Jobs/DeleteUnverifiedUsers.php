<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteUnverifiedUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /** Constructor para inicializar el ID del usuario */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /** Ejecutar el trabajo de eliminación */
    public function handle()
    {
        $user = User::find($this->userId);

        if ($user && $user->estado === 'pendiente') {
            Log::info('Eliminando usuario no verificado', [
                'user_id' => $user->id,
                'email' => $user->correo,
                'created_at' => $user->created_at
            ]);

            // Eliminar registros relacionados en cascada
            if ($user->solicitantes) {
                foreach ($user->solicitantes as $solicitante) {
                    if ($solicitante->tramites) {
                        foreach ($solicitante->tramites as $tramite) {
                            $tramite->documentosSolicitante()->delete();
                            $tramite->detalleTramite()->delete();
                            $tramite->delete();
                        }
                    }
                    $solicitante->delete();
                }
            }

            $user->delete();
            Log::info('Usuario no verificado eliminado exitosamente', ['user_id' => $this->userId]);
        }
    }
} 