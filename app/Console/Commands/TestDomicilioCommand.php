<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tramite;
use App\Models\User;
use App\Models\Solicitante;
use App\Http\Controllers\TramiteSolicitanteController;

class TestDomicilioCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:domicilio {--user-id=1 : ID del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la obtención de datos de domicilio desde las relaciones tramite->detalle_tramite->direccion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        $this->info("=== TESTING DOMICILIO RELATIONS ===");
        $this->info("Usuario ID: {$userId}");
        
        // Buscar usuario
        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado");
            return 1;
        }
        
        $this->info("Usuario encontrado: {$user->email}");
        
        // Buscar solicitante
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        if (!$solicitante) {
            $this->error("No se encontró solicitante para el usuario");
            return 1;
        }
        
        $this->info("Solicitante encontrado: RFC {$solicitante->rfc}");
        
        // Buscar trámites en progreso
        $tramites = Tramite::where('solicitante_id', $solicitante->id)
            ->whereIn('estado', ['Pendiente', 'En Revision'])
            ->with([
                'detalleTramite.direccion.asentamiento.localidad.municipio.estado'
            ])
            ->get();
            
        $this->info("Trámites encontrados: " . $tramites->count());
        
        foreach ($tramites as $tramite) {
            $this->info("\n--- TRAMITE ID: {$tramite->id} ---");
            $this->info("Tipo: {$tramite->tipo_tramite}");
            $this->info("Estado: {$tramite->estado}");
            
            // Verificar detalle_tramite
            if ($tramite->detalleTramite) {
                $this->info("✓ Tiene detalle_tramite (ID: {$tramite->detalleTramite->id})");
                $this->info("  - direccion_id: " . ($tramite->detalleTramite->direccion_id ?? 'NULL'));
                
                // Verificar direccion
                if ($tramite->detalleTramite->direccion) {
                    $direccion = $tramite->detalleTramite->direccion;
                    $this->info("✓ Tiene direccion (ID: {$direccion->id})");
                    $this->info("  - codigo_postal: " . ($direccion->codigo_postal ?? 'NULL'));
                    $this->info("  - calle: " . ($direccion->calle ?? 'NULL'));
                    $this->info("  - numero_exterior: " . ($direccion->numero_exterior ?? 'NULL'));
                    $this->info("  - asentamiento_id: " . ($direccion->asentamiento_id ?? 'NULL'));
                    
                    // Verificar relaciones de ubicación
                    if ($direccion->asentamiento) {
                        $this->info("✓ Tiene asentamiento: " . $direccion->asentamiento->nombre);
                        
                        if ($direccion->asentamiento->localidad) {
                            $this->info("✓ Tiene localidad: " . $direccion->asentamiento->localidad->nombre);
                            
                            if ($direccion->asentamiento->localidad->municipio) {
                                $this->info("✓ Tiene municipio: " . $direccion->asentamiento->localidad->municipio->nombre);
                                
                                if ($direccion->asentamiento->localidad->municipio->estado) {
                                    $this->info("✓ Tiene estado: " . $direccion->asentamiento->localidad->municipio->estado->nombre);
                                } else {
                                    $this->warn("✗ No tiene estado");
                                }
                            } else {
                                $this->warn("✗ No tiene municipio");
                            }
                        } else {
                            $this->warn("✗ No tiene localidad");
                        }
                    } else {
                        $this->warn("✗ No tiene asentamiento");
                    }
                } else {
                    $this->warn("✗ No tiene direccion");
                }
            } else {
                $this->warn("✗ No tiene detalle_tramite");
            }
            
            // Probar el método obtenerDatosDomicilio
            $controller = new TramiteSolicitanteController();
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('obtenerDatosDomicilio');
            $method->setAccessible(true);
            
            $datosDomicilio = $method->invoke($controller, $tramite);
            
            $this->info("\n--- DATOS OBTENIDOS ---");
            $this->table(
                ['Campo', 'Valor'],
                collect($datosDomicilio)->map(function($value, $key) {
                    return [$key, $value ?? 'NULL'];
                })->toArray()
            );
        }
        
        if ($tramites->isEmpty()) {
            $this->warn("No se encontraron trámites en progreso");
        }
        
        return 0;
    }
}
