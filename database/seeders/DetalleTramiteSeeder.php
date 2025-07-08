<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DetalleTramiteSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creando detalles de trámites...');
        
        $jsonPath = public_path('json/detalle_tramite.json');
        if (!File::exists($jsonPath) || 
            !($detalles = json_decode(File::get($jsonPath), true)) || 
            empty($detalles)) {
            $this->command->error('❌ Error al cargar detalles de trámites');
            return;
        }

        foreach ($detalles as $detalle) {
            if (!$this->validarCamposRequeridos($detalle)) {
                continue;
            }

            DB::table('detalle_tramite')->insert([
                'tramite_id' => $detalle['tramite_id'],
                'seccion_id' => $detalle['seccion_id'],
                'estado' => $detalle['estado'] ?? 'pendiente',
                'observaciones' => $detalle['observaciones'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Detalles de trámites creados exitosamente');
    }

    private function validarCamposRequeridos($detalle): bool
    {
        return isset($detalle['tramite_id'], $detalle['seccion_id']);
    }
}