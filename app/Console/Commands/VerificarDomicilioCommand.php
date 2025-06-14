<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\Direccion;
use App\Models\User;
use App\Models\Solicitante;

class VerificarDomicilioCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verificar:domicilio {--user-id=1 : ID del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica los datos de domicilio en la base de datos y diagnostica problemas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        $this->info("=== VERIFICACIÓN DE DOMICILIO ===");
        $this->info("Usuario ID: {$userId}");
        
        // Buscar usuario
        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado");
            return 1;
        }
        
        $this->info("✅ Usuario encontrado: {$user->email}");
        
        // Buscar solicitante
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        if (!$solicitante) {
            $this->error("❌ No se encontró solicitante para el usuario");
            return 1;
        }
        
        $this->info("✅ Solicitante encontrado: RFC {$solicitante->rfc}");
        
        // Buscar trámites
        $tramites = Tramite::where('solicitante_id', $solicitante->id)->get();
        $this->info("📋 Trámites encontrados: " . $tramites->count());
        
        if ($tramites->isEmpty()) {
            $this->error("❌ No hay trámites registrados para este usuario");
            return 1;
        }
        
        foreach ($tramites as $tramite) {
            $this->info("\n--- TRÁMITE ID: {$tramite->id} ---");
            $this->info("Tipo: {$tramite->tipo_tramite}");
            $this->info("Estado: {$tramite->estado}");
            
            // Verificar detalle_tramite
            $detalle = DetalleTramite::where('tramite_id', $tramite->id)->first();
            if (!$detalle) {
                $this->warn("⚠️  No hay detalle_tramite para este trámite");
                continue;
            }
            
            $this->info("✅ Detalle encontrado (ID: {$detalle->id})");
            $this->info("Dirección ID: " . ($detalle->direccion_id ?? 'NULL'));
            
            if (!$detalle->direccion_id) {
                $this->warn("⚠️  No hay direccion_id asignado en detalle_tramite");
                continue;
            }
            
            // Verificar dirección
            $direccion = Direccion::find($detalle->direccion_id);
            if (!$direccion) {
                $this->error("❌ Dirección con ID {$detalle->direccion_id} no existe");
                continue;
            }
            
            $this->info("✅ Dirección encontrada (ID: {$direccion->id})");
            $this->info("Código Postal: {$direccion->codigo_postal}");
            $this->info("Calle: " . ($direccion->calle ?? 'NULL'));
            $this->info("Número Exterior: " . ($direccion->numero_exterior ?? 'NULL'));
            $this->info("Asentamiento ID: " . ($direccion->asentamiento_id ?? 'NULL'));
            
            // Verificar relaciones completas
            $tramiteConRelaciones = Tramite::with([
                'detalleTramite.direccion.asentamiento.localidad.municipio.estado'
            ])->find($tramite->id);
            
            if ($tramiteConRelaciones->detalleTramite && $tramiteConRelaciones->detalleTramite->direccion) {
                $direccionRelacion = $tramiteConRelaciones->detalleTramite->direccion;
                $this->info("✅ Relaciones cargadas correctamente");
                
                if ($direccionRelacion->asentamiento) {
                    $this->info("✅ Asentamiento: " . $direccionRelacion->asentamiento->nombre);
                    
                    if ($direccionRelacion->asentamiento->localidad) {
                        $this->info("✅ Localidad: " . $direccionRelacion->asentamiento->localidad->nombre);
                        
                        if ($direccionRelacion->asentamiento->localidad->municipio) {
                            $this->info("✅ Municipio: " . $direccionRelacion->asentamiento->localidad->municipio->nombre);
                            
                            if ($direccionRelacion->asentamiento->localidad->municipio->estado) {
                                $this->info("✅ Estado: " . $direccionRelacion->asentamiento->localidad->municipio->estado->nombre);
                            } else {
                                $this->warn("⚠️  No hay estado relacionado");
                            }
                        } else {
                            $this->warn("⚠️  No hay municipio relacionado");
                        }
                    } else {
                        $this->warn("⚠️  No hay localidad relacionada");
                    }
                } else {
                    $this->warn("⚠️  No hay asentamiento relacionado");
                }
            } else {
                $this->error("❌ No se pudieron cargar las relaciones");
            }
        }
        
        // Estadísticas generales
        $this->info("\n=== ESTADÍSTICAS GENERALES ===");
        $totalTramites = Tramite::count();
        $tramitesConDetalle = DetalleTramite::count();
        $direccionesTotal = Direccion::count();
        $detallesConDireccion = DetalleTramite::whereNotNull('direccion_id')->count();
        
        $this->info("Total trámites: {$totalTramites}");
        $this->info("Trámites con detalle: {$tramitesConDetalle}");
        $this->info("Total direcciones: {$direccionesTotal}");
        $this->info("Detalles con dirección: {$detallesConDireccion}");
        
        return 0;
    }
} 