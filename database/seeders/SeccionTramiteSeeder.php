<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeccionTramite;

class SeccionTramiteSeeder extends Seeder
{
    public function run()
    {
        $secciones = [
            [
                'nombre' => 'Datos Generales',
                'descripcion' => 'Información básica del solicitante',
                'orden' => 1,
                'es_requerido' => true
            ],
            [
                'nombre' => 'Domicilio',
                'descripcion' => 'Dirección fiscal y de notificaciones',
                'orden' => 2,
                'es_requerido' => true
            ],
            [
                'nombre' => 'Documentos Legales',
                'descripcion' => 'Documentación legal requerida',
                'orden' => 3,
                'es_requerido' => true
            ],
            [
                'nombre' => 'Información Financiera',
                'descripcion' => 'Datos financieros y bancarios',
                'orden' => 4,
                'es_requerido' => true
            ],
            [
                'nombre' => 'Actividades Económicas',
                'descripcion' => 'Actividades y sectores económicos',
                'orden' => 5,
                'es_requerido' => true
            ],
            [
                'nombre' => 'Representantes',
                'descripcion' => 'Información de representantes legales',
                'orden' => 6,
                'es_requerido' => true
            ],
            [
                'nombre' => 'Documentos Adicionales',
                'descripcion' => 'Documentación complementaria',
                'orden' => 7,
                'es_requerido' => false
            ]
        ];

        foreach ($secciones as $seccion) {
            SeccionTramite::create($seccion);
        }
    }
} 