<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use DateTime;

class ProveedorSeeder extends Seeder
{
    private $idsValidos = [];

    public function run()
    {
        $this->command->info('Creando proveedores...');
        
        $jsonPath = public_path('json/proveedores.json');
        if (!File::exists($jsonPath) || 
            !($proveedores = json_decode(File::get($jsonPath), true)) || 
            empty($proveedores)) {
            $this->command->error('❌ Error al cargar proveedores');
            return;
        }

        $this->cargarIdsValidos();
        
        foreach ($proveedores as $proveedor) {
            if (!$this->validarCamposRequeridos($proveedor) || 
                !in_array($proveedor['id_solicitante'], $this->idsValidos['solicitante'])) {
                continue;
            }

            $estado = $this->calcularEstado($proveedor['fecha_vencimiento'], new DateTime());
            
            DB::table('proveedor')->insert([
                'pv' => $proveedor['proveedor_id'],
                'solicitante_id' => $proveedor['id_solicitante'],
                'razon_social' => $proveedor['razon_social'],
                'rfc' => $proveedor['rfc'],
                'fecha_registro' => $this->validarFecha($proveedor['fecha_registro']),
                'fecha_vencimiento' => $this->validarFecha($proveedor['fecha_vencimiento']),
                'estado' => $estado,
                'observaciones' => $proveedor['observaciones'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Proveedores creados exitosamente');
    }

    private function cargarIdsValidos()
    {
        $this->idsValidos = [
            'solicitante' => DB::table('solicitante')->pluck('id')->toArray(),
        ];
    }

    private function validarCamposRequeridos($proveedor): bool
    {
        return isset($proveedor['proveedor_id'], 
                    $proveedor['id_solicitante'], 
                    $proveedor['razon_social'], 
                    $proveedor['rfc'], 
                    $proveedor['estado'], 
                    $proveedor['fecha_registro'], 
                    $proveedor['fecha_vencimiento']);
    }

    private function validarFecha($fecha)
    {
        return DateTime::createFromFormat('Y-m-d', $fecha) ? $fecha : null;
    }

    private function calcularEstado($fechaVencimiento, DateTime $today): string
    {
        if (!$fechaVencimiento || !DateTime::createFromFormat('Y-m-d', $fechaVencimiento)) {
            return 'Inactivo';
        }

        $vencimientoDate = new DateTime($fechaVencimiento);
        if ($vencimientoDate <= $today) {
            return 'Inactivo';
        }

        $diasParaVencer = $today->diff($vencimientoDate)->days;
        return $diasParaVencer <= 7 ? 'Pendiente Renovacion' : 'Activo';
    }
}