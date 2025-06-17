<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SeccionTramite;

class SeccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $secciones = [
            [
                'nombre' => 'Datos Generales',
                'descripcion' => 'Revisión de datos generales del solicitante',
                'orden' => 1,
                'es_requerido' => true,
            ],
            [
                'nombre' => 'Domicilio',
                'descripcion' => 'Verificación de información de domicilio',
                'orden' => 2,
                'es_requerido' => true,
            ],
            [
                'nombre' => 'Documentos',
                'descripcion' => 'Revisión de documentos presentados',
                'orden' => 3,
                'es_requerido' => true,
            ],
            [
                'nombre' => 'Actividades',
                'descripcion' => 'Verificación de actividades económicas',
                'orden' => 4,
                'es_requerido' => true,
            ],
            [
                'nombre' => 'Accionistas',
                'descripcion' => 'Revisión de información de accionistas',
                'orden' => 5,
                'es_requerido' => true,
            ],
            [
                'nombre' => 'Finalización',
                'descripcion' => 'Revisión final y aprobación del trámite',
                'orden' => 6,
                'es_requerido' => true,
            ],
        ];

        foreach ($secciones as $seccion) {
            SeccionTramite::firstOrCreate(
                ['nombre' => $seccion['nombre']],
                $seccion
            );
        }
    }
}
