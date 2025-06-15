<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tramite;
use App\Http\Controllers\DetalleTramiteController;
use Illuminate\Support\Facades\Log;

class TestDomicilioFromTramiteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:domicilio-tramite {tramite_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la obtención de código postal desde un trámite específico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tramiteId = $this->argument('tramite_id');

        if ($tramiteId) {
            $this->testSingleTramite($tramiteId);
        } else {
            $this->testAllTramites();
        }
    }

    private function testSingleTramite($tramiteId)
    {
        $this->info("=== TESTING TRÁMITE ID: {$tramiteId} ===");
        
        $tramite = Tramite::with(['detalleTramite.direccion.asentamiento'])->find($tramiteId);
        
        if (!$tramite) {
            $this->error("❌ Trámite no encontrado");
            return;
        }

        $this->info("✅ Trámite encontrado:");
        $this->info("  - Tipo: {$tramite->tipo_tramite}");
        $this->info("  - Estado: {$tramite->estado}");
        $this->info("  - Progreso: {$tramite->progreso_tramite}");

        // Test usando DetalleTramiteController
        $detalleTramiteController = new DetalleTramiteController();
        
        $this->info("\n--- USANDO DetalleTramiteController ---");
        
        // 1. Obtener código postal
        $codigoPostal = $detalleTramiteController->getCodigoPostalByTramiteId($tramiteId);
        $this->info("📮 Código Postal: " . ($codigoPostal ?? 'NULL'));

        // 2. Obtener datos completos de domicilio
        $datosDomicilio = $detalleTramiteController->getDatosDomicilioByTramiteId($tramiteId);
        
        if ($datosDomicilio) {
            $this->info("✅ Datos de domicilio obtenidos:");
            $this->info("  - Código Postal: " . ($datosDomicilio['codigo_postal'] ?? 'NULL'));
            $this->info("  - Estado: " . ($datosDomicilio['estado'] ?? 'NULL'));
            $this->info("  - Municipio: " . ($datosDomicilio['municipio'] ?? 'NULL'));
            $this->info("  - Colonia: " . ($datosDomicilio['colonia'] ?? 'NULL'));
            $this->info("  - Calle: " . ($datosDomicilio['calle'] ?? 'NULL'));
            $this->info("  - Número Exterior: " . ($datosDomicilio['numero_exterior'] ?? 'NULL'));
        } else {
            $this->error("❌ No se pudieron obtener datos de domicilio");
        }

        // 3. Test API endpoint
        $this->info("\n--- TESTING API ENDPOINT ---");
        
        try {
            $apiResponse = $detalleTramiteController->getDomicilioApi($tramiteId);
            $responseData = $apiResponse->getData(true);
            
            $this->info("📡 Respuesta API:");
            $this->info("  - Success: " . ($responseData['success'] ? 'true' : 'false'));
            
            if ($responseData['success'] && isset($responseData['domicilio'])) {
                $domicilio = $responseData['domicilio'];
                $this->info("  - Código Postal API: " . ($domicilio['codigo_postal'] ?? 'NULL'));
                $this->info("  - Estado API: " . ($domicilio['estado'] ?? 'NULL'));
                $this->info("  - Municipio API: " . ($domicilio['municipio'] ?? 'NULL'));
            } else {
                $this->warn("⚠️ API retornó éxito=false o sin datos de domicilio");
                if (isset($responseData['message'])) {
                    $this->info("  - Mensaje: " . $responseData['message']);
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error en API: " . $e->getMessage());
        }

        // 4. Verificar estructura de base de datos
        $this->info("\n--- VERIFICACIÓN DE BASE DE DATOS ---");
        
        if ($tramite->detalleTramite) {
            $this->info("✅ Tiene detalle_tramite (ID: {$tramite->detalleTramite->id})");
            $this->info("  - direccion_id: " . ($tramite->detalleTramite->direccion_id ?? 'NULL'));
            
            if ($tramite->detalleTramite->direccion) {
                $direccion = $tramite->detalleTramite->direccion;
                $this->info("✅ Tiene direccion (ID: {$direccion->id})");
                $this->info("  - codigo_postal: {$direccion->codigo_postal}");
                $this->info("  - asentamiento_id: " . ($direccion->asentamiento_id ?? 'NULL'));
                
                if ($direccion->asentamiento) {
                    $this->info("✅ Tiene asentamiento: " . $direccion->asentamiento->nombre);
                } else {
                    $this->warn("⚠️ No tiene asentamiento relacionado");
                }
            } else {
                $this->warn("⚠️ No tiene direccion relacionada");
            }
        } else {
            $this->warn("⚠️ No tiene detalle_tramite");
        }
    }

    private function testAllTramites()
    {
        $this->info("=== TESTING TODOS LOS TRÁMITES ===");
        
        $tramites = Tramite::with(['detalleTramite.direccion'])->get();
        
        $this->info("Total de trámites: " . $tramites->count());
        
        $conDomicilio = 0;
        $sinDomicilio = 0;
        
        foreach ($tramites as $tramite) {
            $detalleTramiteController = new DetalleTramiteController();
            $codigoPostal = $detalleTramiteController->getCodigoPostalByTramiteId($tramite->id);
            
            if ($codigoPostal) {
                $conDomicilio++;
                $this->info("✅ Trámite {$tramite->id}: CP={$codigoPostal}");
            } else {
                $sinDomicilio++;
                $this->warn("⚠️ Trámite {$tramite->id}: Sin código postal");
            }
        }
        
        $this->info("\n=== RESUMEN ===");
        $this->info("Con domicilio: {$conDomicilio}");
        $this->info("Sin domicilio: {$sinDomicilio}");
        
        if ($sinDomicilio > 0) {
            $this->info("\nEjecutar con ID específico para ver detalles:");
            $this->info("php artisan test:domicilio-tramite [ID]");
        }
    }
} 