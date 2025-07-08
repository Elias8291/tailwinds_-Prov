<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SolicitanteSeeder extends Seeder
{
    private const TIPOS_PERSONA = ['Física', 'Moral'];
    private $idsValidos = [];

    public function run()
    {
        $this->command->info('Creando solicitantes...');

        $jsonPath = public_path('json/solicitante.json');

        if (!File::exists($jsonPath) || 
            !($solicitantes = json_decode(File::get($jsonPath), true)) || 
            empty($solicitantes)) {
            $this->command->error('❌ Error al cargar solicitantes');
            return;
        }

        $this->cargarIdsValidos();
        
        foreach ($solicitantes as $solicitante) {
            if (!$this->validarCamposRequeridos($solicitante)) {
                continue;
            }

            DB::table('solicitante')->insert([
                'usuario_id' => $this->validarId('user', $solicitante['usuario_id'] ?? null),
                'tipo_persona' => $this->validarTipoPersona($solicitante['tipo_persona']),
                'curp' => $solicitante['curp'] ?? null,
                'rfc' => $solicitante['rfc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Solicitantes creados exitosamente');
    }

    private function cargarIdsValidos()
    {
        $this->idsValidos = [
            'user' => DB::table('users')->pluck('id')->toArray(),
        ];
    }

    private function validarCamposRequeridos($solicitante): bool
    {
        return isset($solicitante['tipo_persona'], $solicitante['rfc']);
    }

    private function validarId(string $tipo, $id)
    {
        if ($id === null) {
            return null;
        }

        return in_array($id, $this->idsValidos[$tipo]) ? $id : null;
    }

    private function validarTipoPersona(string $tipo): string
    {
        return in_array($tipo, self::TIPOS_PERSONA) ? $tipo : 'Física';
    }
}