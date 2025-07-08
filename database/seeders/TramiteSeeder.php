<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TramiteSeeder extends Seeder
{
    private const TIPOS_TRAMITE = ['Inscripcion', 'Renovacion', 'Actualizacion'];
    private const ESTADOS = ['Pendiente', 'En Revision', 'Aprobado', 'Rechazado'];
    private $idsValidos = [];

    public function run()
    {
        $this->command->info('Creando trámites...');
        
        $jsonPath = public_path('json/tramite.json');
        if (!File::exists($jsonPath)) {
            $this->command->error('❌ Error al cargar trámites: archivo no encontrado');
            return;
        }

        $jsonContent = File::get($jsonPath);
        $tramites = json_decode($jsonContent, true);

        if (!is_array($tramites)) {
            $this->command->error('❌ Error al cargar trámites: formato inválido');
            return;
        }

        $this->cargarIdsValidos();

        foreach ($tramites as $tramite) {
            if (!$this->validarCamposRequeridos($tramite)) {
                continue;
            }

            DB::table('tramite')->insert([
                'solicitante_id' => $this->validarId('solicitante', $tramite['solicitante_id'] ?? null),
                'tipo_tramite' => $this->validarTipoTramite($tramite['tipo_tramite']),
                'estado' => $this->validarEstado($tramite['estado'] ?? 'pendiente'),
                'progreso_tramite' => $this->validarProgreso($tramite['progreso_tramite'] ?? 0),
                'revisado_por' => $this->validarId('user', $tramite['revisado_por'] ?? null),
                'fecha_revision' => $tramite['fecha_revision'] ?? null,
                'fecha_inicio' => $tramite['fecha_inicio'] ?? null,
                'fecha_finalizacion' => $tramite['fecha_finalizacion'] ?? null,
                'observaciones' => $tramite['observaciones'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Trámites creados exitosamente');
    }

    private function cargarIdsValidos()
    {
        $this->idsValidos = [
            'solicitante' => DB::table('solicitante')->pluck('id')->toArray(),
            'user' => DB::table('users')->pluck('id')->toArray(),
        ];
    }

    private function validarCamposRequeridos($tramite): bool
    {
        return isset($tramite['solicitante_id'], $tramite['tipo_tramite']);
    }

    private function validarId(string $tipo, $id)
    {
        if ($id === null) {
            return null;
        }

        return in_array($id, $this->idsValidos[$tipo]) ? $id : null;
    }

    private function validarTipoTramite(string $tipo): string
    {
        return in_array($tipo, self::TIPOS_TRAMITE) ? $tipo : 'Inscripcion';
    }

    private function validarEstado(string $estado): string
    {
        return in_array($estado, self::ESTADOS) ? $estado : 'Pendiente';
    }

    private function validarProgreso($progreso): int
    {
        if (!is_numeric($progreso)) {
            return 0;
        }
        return max(0, min(100, (int)$progreso));
    }
}