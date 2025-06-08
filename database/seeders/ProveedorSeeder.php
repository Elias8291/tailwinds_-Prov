<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use DateTime;

class ProveedorSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = public_path('json/proveedores.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('The proveedores.json file does not exist!');
            return;
        }

        $proveedores = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('JSON parsing error: ' . json_last_error_msg());
            return;
        }

        if (empty($proveedores)) {
            $this->command->error('No data found in proveedores.json!');
            return;
        }

        $solicitanteIds = DB::table('solicitante')->pluck('id')->toArray();
        $today = new DateTime();

        foreach ($proveedores as $proveedor) {
            // Skip entries missing required keys
            if (!isset($proveedor['proveedor_id']) || 
                !isset($proveedor['id_solicitante']) || 
                !isset($proveedor['estado']) || 
                !isset($proveedor['fecha_registro']) || 
                !isset($proveedor['fecha_vencimiento'])) {
                $this->command->warn('Skipping invalid entry: Missing required fields');
                continue;
            }

            // Validate solicitante_id
            $solicitanteId = null;
            if (in_array($proveedor['id_solicitante'], $solicitanteIds)) {
                $solicitanteId = $proveedor['id_solicitante'];
            } else {
                $this->command->warn('Invalid id_solicitante in JSON: ' . $proveedor['id_solicitante'] . '. Skipping entry.');
                continue;
            }

            // Validate fecha_registro
            $fechaRegistro = $proveedor['fecha_registro'];
            if ($fechaRegistro && !DateTime::createFromFormat('Y-m-d', $fechaRegistro)) {
                $this->command->warn('Invalid fecha_registro in JSON: ' . $proveedor['fecha_registro'] . '. Setting fecha_registro to null.');
                $fechaRegistro = null;
            }

            // Validate fecha_vencimiento and determine estado
            $fechaVencimiento = $proveedor['fecha_vencimiento'];
            $estado = 'Inactivo'; // Default estado

            if ($fechaVencimiento && DateTime::createFromFormat('Y-m-d', $fechaVencimiento)) {
                $vencimientoDate = new DateTime($fechaVencimiento);
                
                if ($vencimientoDate > $today) {
                    // Si la fecha de vencimiento es futura
                    $diasParaVencer = $today->diff($vencimientoDate)->days;
                    
                    if ($diasParaVencer <= 7) {
                        $estado = 'Pendiente Renovacion';
                    } else {
                        $estado = 'Activo';
                    }
                } else {
                    // Si la fecha ya pasó
                    $estado = 'Inactivo';
                }
            } else {
                $this->command->warn('Invalid fecha_vencimiento in JSON: ' . $proveedor['fecha_vencimiento'] . '. Setting estado to Inactivo.');
                $fechaVencimiento = null;
            }

            // Debug information
            $this->command->info("Processing proveedor: {$proveedor['proveedor_id']}");
            $this->command->info("Original estado: {$proveedor['estado']}");
            $this->command->info("Calculated estado: {$estado}");
            $this->command->info("Vencimiento: {$fechaVencimiento}");

            DB::table('proveedor')->insert([
                'pv' => $proveedor['proveedor_id'],
                'solicitante_id' => $solicitanteId,
                'fecha_registro' => $fechaRegistro,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => $estado,
                'observaciones' => $proveedor['observaciones'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Proveedor records seeded successfully!');
    }
}