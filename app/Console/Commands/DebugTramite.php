<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\ContactoSolicitante;

class DebugTramite extends Command
{
    protected $signature = 'debug:tramite {id?}';
    protected $description = 'Debug datos de un trámite específico';

    public function handle()
    {
        $tramiteId = $this->argument('id');
        
        if (!$tramiteId) {
            // Mostrar trámites disponibles
            $this->info('Trámites disponibles:');
            $tramites = Tramite::with('solicitante')->take(10)->get();
            
            foreach ($tramites as $tramite) {
                $solicitante = $tramite->solicitante;
                $this->line("ID: {$tramite->id} - RFC: " . ($solicitante ? $solicitante->rfc : 'Sin RFC') . " - Tipo: {$tramite->tipo_tramite}");
            }
            
            $tramiteId = $this->ask('¿Qué ID de trámite quieres debuggear?');
        }
        
        $tramite = Tramite::find($tramiteId);
        
        if (!$tramite) {
            $this->error("Trámite con ID {$tramiteId} no encontrado");
            return 1;
        }
        
        $this->info("=== DEBUGGING TRÁMITE ID: {$tramite->id} ===");
        
        // Datos del trámite
        $this->info("📋 DATOS DEL TRÁMITE:");
        $this->line("  - ID: {$tramite->id}");
        $this->line("  - Tipo: {$tramite->tipo_tramite}");
        $this->line("  - Estado: {$tramite->estado}");
        $this->line("  - Solicitante ID: {$tramite->solicitante_id}");
        
        // Datos del solicitante
        $solicitante = $tramite->solicitante;
        $this->info("👤 DATOS DEL SOLICITANTE:");
        if ($solicitante) {
            $this->line("  - ID: {$solicitante->id}");
            $this->line("  - RFC: {$solicitante->rfc}");
            $this->line("  - Tipo: {$solicitante->tipo_persona}");
            $this->line("  - Objeto Social: " . ($solicitante->objeto_social ?: 'VACÍO'));
            $this->line("  - Razón Social: " . ($solicitante->razon_social ?: 'VACÍO'));
        } else {
            $this->error("  - NO HAY SOLICITANTE ASOCIADO");
        }
        
        // Datos del detalle trámite
        $detalle = $tramite->detalleTramite;
        $this->info("📝 DATOS DEL DETALLE TRÁMITE:");
        if ($detalle) {
            $this->line("  - ID: {$detalle->id}");
            $this->line("  - Razón Social: " . ($detalle->razon_social ?: 'VACÍO'));
            $this->line("  - Email: " . ($detalle->email ?: 'VACÍO'));
            $this->line("  - Teléfono: " . ($detalle->telefono ?: 'VACÍO'));
            $this->line("  - Contacto ID: " . ($detalle->contacto_id ?: 'NULL'));
            $this->line("  - Sitio Web: " . ($detalle->sitio_web ?: 'VACÍO'));
        } else {
            $this->error("  - NO HAY DETALLE TRÁMITE");
        }
        
        // Datos del contacto
        $contacto = $detalle ? $detalle->contacto : null;
        $this->info("📞 DATOS DEL CONTACTO (RELACIÓN):");
        if ($contacto) {
            $this->line("  - ID: {$contacto->id}");
            $this->line("  - Nombre: " . ($contacto->nombre ?: 'VACÍO'));
            $this->line("  - Puesto: " . ($contacto->puesto ?: 'VACÍO'));
            $this->line("  - Email: " . ($contacto->email ?: 'VACÍO'));
            $this->line("  - Teléfono: " . ($contacto->telefono ?: 'VACÍO'));
        } else {
            $this->error("  - NO HAY CONTACTO VÍA RELACIÓN");
        }
        
        // Buscar contacto directamente si hay ID
        if ($detalle && $detalle->contacto_id) {
            $contactoDirecto = ContactoSolicitante::find($detalle->contacto_id);
            $this->info("📞 DATOS DEL CONTACTO (DIRECTO):");
            if ($contactoDirecto) {
                $this->line("  - ID: {$contactoDirecto->id}");
                $this->line("  - Nombre: " . ($contactoDirecto->nombre ?: 'VACÍO'));
                $this->line("  - Puesto: " . ($contactoDirecto->puesto ?: 'VACÍO'));
                $this->line("  - Email: " . ($contactoDirecto->email ?: 'VACÍO'));
                $this->line("  - Teléfono: " . ($contactoDirecto->telefono ?: 'VACÍO'));
            } else {
                $this->error("  - CONTACTO NO ENCONTRADO CON ID: {$detalle->contacto_id}");
            }
        }
        
        // Actividades
        $actividades = $tramite->actividades;
        $this->info("🎯 ACTIVIDADES:");
        $this->line("  - Total: " . $actividades->count());
        foreach ($actividades as $actividad) {
            $this->line("    * ID: {$actividad->id} - {$actividad->nombre} (Sector: {$actividad->sector_id})");
        }
        
        $this->info("=== FIN DEBUG ===");
        
        return 0;
    }
} 