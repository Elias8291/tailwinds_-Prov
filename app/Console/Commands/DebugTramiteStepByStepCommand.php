<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\Direccion;
use Illuminate\Support\Facades\DB;

class DebugTramiteStepByStepCommand extends Command
{
    protected $signature = 'debug:tramite-step {tramite_id?}';
    protected $description = 'Debug paso a paso la cadena tramite -> detalle_tramite -> direccion';

    public function handle()
    {
        $tramiteId = $this->argument('tramite_id');

        if ($tramiteId) {
            $this->debugSingleTramite($tramiteId);
        } else {
            $this->debugAllTramites();
        }
    }

    private function debugSingleTramite($tramiteId)
    {
        $this->info("🔍 === DEBUG PASO A PASO PARA TRÁMITE {$tramiteId} ===");

        // PASO 1: Verificar si existe el trámite
        $this->info("📋 PASO 1: Verificando trámite en tabla 'tramite'");
        $tramite = DB::table('tramite')->where('id', $tramiteId)->first();
        
        if (!$tramite) {
            $this->error("❌ No existe trámite con ID {$tramiteId}");
            return;
        }

        $this->info("✅ Trámite encontrado:");
        $this->info("   - ID: {$tramite->id}");
        $this->info("   - Tipo: {$tramite->tipo_tramite}");
        $this->info("   - Estado: {$tramite->estado}");
        $this->info("   - Progreso: {$tramite->progreso_tramite}");
        $this->info("   - Solicitante ID: {$tramite->solicitante_id}");

        // PASO 2: Verificar si existe detalle_tramite
        $this->info("\n📋 PASO 2: Verificando detalle_tramite");
        $detalleTramite = DB::table('detalle_tramite')->where('tramite_id', $tramiteId)->first();
        
        if (!$detalleTramite) {
            $this->error("❌ No existe detalle_tramite para tramite_id {$tramiteId}");
            $this->info("💡 Para crear un detalle_tramite de prueba:");
            $this->info("   INSERT INTO detalle_tramite (tramite_id, razon_social, created_at, updated_at) VALUES ({$tramiteId}, 'Empresa de Prueba', NOW(), NOW());");
            return;
        }

        $this->info("✅ detalle_tramite encontrado:");
        $this->info("   - ID: {$detalleTramite->id}");
        $this->info("   - Tramite ID: {$detalleTramite->tramite_id}");
        $this->info("   - Razón Social: {$detalleTramite->razon_social}");
        $this->info("   - Dirección ID: " . ($detalleTramite->direccion_id ?? 'NULL'));

        // PASO 3: Verificar direccion_id
        if (!$detalleTramite->direccion_id) {
            $this->error("❌ detalle_tramite no tiene direccion_id asignado");
            $this->info("💡 Para crear una dirección de prueba y asignarla:");
            $this->info("   INSERT INTO direccion (codigo_postal, calle, numero_exterior, created_at, updated_at) VALUES (44100, 'Calle de Prueba', '123', NOW(), NOW());");
            $this->info("   -- Luego obtener el ID de la dirección creada y actualizar:");
            $this->info("   UPDATE detalle_tramite SET direccion_id = [ID_DE_DIRECCION] WHERE id = {$detalleTramite->id};");
            return;
        }

        // PASO 4: Verificar si existe la dirección
        $this->info("\n📋 PASO 3: Verificando dirección ID {$detalleTramite->direccion_id}");
        $direccion = DB::table('direccion')->where('id', $detalleTramite->direccion_id)->first();
        
        if (!$direccion) {
            $this->error("❌ No existe dirección con ID {$detalleTramite->direccion_id}");
            $this->info("💡 Para crear la dirección faltante:");
            $this->info("   INSERT INTO direccion (id, codigo_postal, calle, numero_exterior, created_at, updated_at) VALUES ({$detalleTramite->direccion_id}, 44100, 'Calle de Prueba', '123', NOW(), NOW());");
            return;
        }

        $this->info("✅ Dirección encontrada:");
        $this->info("   - ID: {$direccion->id}");
        $this->info("   - Código Postal: {$direccion->codigo_postal}");
        $this->info("   - Calle: " . ($direccion->calle ?? 'NULL'));
        $this->info("   - Número Exterior: " . ($direccion->numero_exterior ?? 'NULL'));
        $this->info("   - Asentamiento ID: " . ($direccion->asentamiento_id ?? 'NULL'));

        // PASO 5: Verificar asentamiento si existe
        if ($direccion->asentamiento_id) {
            $this->info("\n📋 PASO 4: Verificando asentamiento ID {$direccion->asentamiento_id}");
            $asentamiento = DB::table('asentamiento')->where('id', $direccion->asentamiento_id)->first();
            
            if ($asentamiento) {
                $this->info("✅ Asentamiento encontrado:");
                $this->info("   - ID: {$asentamiento->id}");
                $this->info("   - Nombre: {$asentamiento->nombre}");
                $this->info("   - Código Postal: {$asentamiento->codigo_postal}");
            } else {
                $this->warn("⚠️ Asentamiento ID {$direccion->asentamiento_id} no existe en tabla asentamiento");
            }
        }

        $this->info("\n🎉 === RESUMEN FINAL ===");
        $this->info("✅ Cadena completa: tramite -> detalle_tramite -> direccion");
        $this->info("📮 Código postal disponible: {$direccion->codigo_postal}");
        $this->info("🏠 Dirección: " . ($direccion->calle ?? 'Sin calle') . " " . ($direccion->numero_exterior ?? 'S/N'));
        
        $this->info("\n🧪 Ahora puedes probar:");
        $this->info("   php artisan test:domicilio-tramite {$tramiteId}");
    }

    private function debugAllTramites()
    {
        $this->info("🔍 === VERIFICANDO TODOS LOS TRÁMITES ===");
        
        $tramites = DB::table('tramite')->get();
        $this->info("Total de trámites: " . $tramites->count());

        $conCadenaCompleta = 0;
        $sinDetalle = 0;
        $sinDireccion = 0;
        $conDireccionIncompleta = 0;

        foreach ($tramites as $tramite) {
            $detalle = DB::table('detalle_tramite')->where('tramite_id', $tramite->id)->first();
            
            if (!$detalle) {
                $sinDetalle++;
                $this->line("⚠️ Trámite {$tramite->id}: Sin detalle_tramite");
                continue;
            }

            if (!$detalle->direccion_id) {
                $sinDireccion++;
                $this->line("⚠️ Trámite {$tramite->id}: Sin direccion_id");
                continue;
            }

            $direccion = DB::table('direccion')->where('id', $detalle->direccion_id)->first();
            
            if (!$direccion) {
                $conDireccionIncompleta++;
                $this->line("❌ Trámite {$tramite->id}: direccion_id={$detalle->direccion_id} no existe");
                continue;
            }

            $conCadenaCompleta++;
            $this->line("✅ Trámite {$tramite->id}: CP={$direccion->codigo_postal}");
        }

        $this->info("\n=== ESTADÍSTICAS ===");
        $this->info("✅ Con cadena completa: {$conCadenaCompleta}");
        $this->info("⚠️ Sin detalle_tramite: {$sinDetalle}");
        $this->info("⚠️ Sin direccion_id: {$sinDireccion}");
        $this->info("❌ Con direccion_id inválido: {$conDireccionIncompleta}");

        if ($conCadenaCompleta > 0) {
            $primerTramiteCompleto = null;
            foreach ($tramites as $tramite) {
                $detalle = DB::table('detalle_tramite')->where('tramite_id', $tramite->id)->first();
                if ($detalle && $detalle->direccion_id) {
                    $direccion = DB::table('direccion')->where('id', $detalle->direccion_id)->first();
                    if ($direccion) {
                        $primerTramiteCompleto = $tramite->id;
                        break;
                    }
                }
            }
            
            if ($primerTramiteCompleto) {
                $this->info("\n🧪 Para probar con un trámite que tiene datos completos:");
                $this->info("   php artisan debug:tramite-step {$primerTramiteCompleto}");
            }
        }
    }
} 