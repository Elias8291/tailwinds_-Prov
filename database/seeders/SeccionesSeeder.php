<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeccionesSeeder extends Seeder
{
    public function run()
    {
        // Limpiar la tabla primero
        DB::table('secciones')->delete();
        
        // Insertar las secciones
        $secciones = [
            [
                'id' => 1,
                'nombre' => 'Datos Generales',
                'descripcion' => 'Información básica del solicitante',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'nombre' => 'Domicilio',
                'descripcion' => 'Dirección fiscal y de notificaciones',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'nombre' => 'Constitución',
                'descripcion' => 'Información de constitución de la empresa',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'nombre' => 'Accionistas',
                'descripcion' => 'Información de accionistas o socios',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'nombre' => 'Apoderado Legal',
                'descripcion' => 'Información del apoderado legal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'nombre' => 'Documentos',
                'descripcion' => 'Documentación requerida para el trámite',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('secciones')->insert($secciones);
    }
} 