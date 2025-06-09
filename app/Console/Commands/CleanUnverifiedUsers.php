<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanUnverifiedUsers extends Command
{
    protected $signature = 'users:clean-unverified';
    protected $description = 'Clean unverified users older than 72 hours';

    /** Execute the console command */
    public function handle()
    {
        $cutoffDate = now()->subHours(72);
        
        $unverifiedUsers = User::where('estado', 'pendiente')
                              ->where('created_at', '<', $cutoffDate)
                              ->get();

        $deletedCount = 0;

        foreach ($unverifiedUsers as $user) {
            $this->info("Eliminando usuario no verificado: {$user->correo} (ID: {$user->id})");
            
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
            $deletedCount++;
            
            Log::info('Usuario no verificado eliminado por comando', [
                'user_id' => $user->id,
                'email' => $user->correo
            ]);
        }

        $this->info("Se eliminaron {$deletedCount} usuarios no verificados.");
        
        return 0;
    }
} 