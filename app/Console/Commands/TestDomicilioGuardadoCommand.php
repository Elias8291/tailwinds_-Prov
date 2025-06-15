<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tramite;
use App\Http\Controllers\Formularios\DomicilioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestDomicilioGuardadoCommand extends Command
{
    protected $signature = 'test:domicilio-guardado {tramite_id}';
    protected $description = 'Prueba el guardado y carga de datos de domicilio usando DomicilioController';

    public function handle()
    {
        $tramiteId = $this->argument('tramite_id');
        
        $this->info("🧪 === TESTING GUARDADO Y CARGA DE DOMICILIO ===");
        $this->info("🔍 Tramite ID: {$tramiteId}");
        
        // 1. Buscar el trámite
        $tramite = Tramite::find($tramiteId);
        if (!$tramite) {
            $this->error("❌ Trámite no encontrado");
            return;
        }
        
        $this->info("✅ Trámite encontrado: {$tramite->tipo_tramite}");
        
        // 2. Crear controlador
        $controller = new DomicilioController();
        
        // 3. Probar obtener datos ANTES del guardado
        $this->info("\n📋 === PASO 1: OBTENER DATOS EXISTENTES ===");
        $datosExistentes = $controller->obtenerDatos($tramite);
        $this->info("Datos existentes:");
        $this->info("  - Código Postal: " . ($datosExistentes['codigo_postal'] ?? 'NULL'));
        $this->info("  - Estado: " . ($datosExistentes['estado'] ?? 'NULL'));
        $this->info("  - Municipio: " . ($datosExistentes['municipio'] ?? 'NULL'));
        $this->info("  - Calle: " . ($datosExistentes['calle'] ?? 'NULL'));
        
        // 4. Simular datos de prueba para guardar
        $this->info("\n💾 === PASO 2: SIMULAR GUARDADO ===");
        $datosPrueba = [
            'codigo_postal' => '01050',
            'colonia' => '25875', // ID del asentamiento
            'calle' => 'Calle de Prueba Actualizada',
            'numero_exterior' => '123',
            'numero_interior' => 'A',
            'entre_calle_1' => 'Calle Norte',
            'entre_calle_2' => 'Calle Sur'
        ];
        
        // Crear request simulado
        $request = new Request($datosPrueba);
        
        try {
            $resultado = $controller->guardar($request, $tramite);
            
            if ($resultado) {
                $this->info("✅ Guardado exitoso");
            } else {
                $this->error("❌ Error en el guardado");
                return;
            }
        } catch (\Exception $e) {
            $this->error("❌ Excepción durante el guardado: " . $e->getMessage());
            return;
        }
        
        // 5. Probar obtener datos DESPUÉS del guardado
        $this->info("\n📋 === PASO 3: VERIFICAR DATOS GUARDADOS ===");
        $datosGuardados = $controller->obtenerDatos($tramite);
        $this->info("Datos después del guardado:");
        $this->info("  - Código Postal: " . ($datosGuardados['codigo_postal'] ?? 'NULL'));
        $this->info("  - Estado: " . ($datosGuardados['estado'] ?? 'NULL'));
        $this->info("  - Municipio: " . ($datosGuardados['municipio'] ?? 'NULL'));
        $this->info("  - Calle: " . ($datosGuardados['calle'] ?? 'NULL'));
        $this->info("  - Número Exterior: " . ($datosGuardados['numero_exterior'] ?? 'NULL'));
        $this->info("  - Número Interior: " . ($datosGuardados['numero_interior'] ?? 'NULL'));
        $this->info("  - Entre Calle 1: " . ($datosGuardados['entre_calle_1'] ?? 'NULL'));
        $this->info("  - Entre Calle 2: " . ($datosGuardados['entre_calle_2'] ?? 'NULL'));
        
        // 6. Verificar que los datos se guardaron correctamente
        $this->info("\n🔍 === PASO 4: VERIFICACIÓN ===");
        $verificaciones = [
            'Código Postal' => $datosGuardados['codigo_postal'] === '01050',
            'Calle' => $datosGuardados['calle'] === 'Calle de Prueba Actualizada',
            'Número Exterior' => $datosGuardados['numero_exterior'] === '123',
            'Número Interior' => $datosGuardados['numero_interior'] === 'A',
            'Entre Calle 1' => $datosGuardados['entre_calle_1'] === 'Calle Norte',
            'Entre Calle 2' => $datosGuardados['entre_calle_2'] === 'Calle Sur'
        ];
        
        $todosCorrecto = true;
        foreach ($verificaciones as $campo => $correcto) {
            if ($correcto) {
                $this->info("✅ {$campo}: CORRECTO");
            } else {
                $this->error("❌ {$campo}: INCORRECTO");
                $todosCorrecto = false;
            }
        }
        
        if ($todosCorrecto) {
            $this->info("\n🎉 === RESULTADO FINAL ===");
            $this->info("✅ TODAS LAS PRUEBAS PASARON EXITOSAMENTE");
            $this->info("✅ El sistema de guardado y carga funciona correctamente");
        } else {
            $this->error("\n❌ === RESULTADO FINAL ===");
            $this->error("❌ ALGUNAS PRUEBAS FALLARON");
        }
    }
} 