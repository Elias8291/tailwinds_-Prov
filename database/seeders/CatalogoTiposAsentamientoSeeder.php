<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoTiposAsentamientoSeeder extends Seeder
{
    public function run()
    {
        $tiposAsentamiento = [
            'Colonia',
            'Fraccionamiento',
            'Condominio',
            'Unidad habitacional',
            'Barrio',
            'Equipamiento',
            'Zona comercial',
            'Rancho',
            'Ranchería',
            'Zona industrial',
            'Granja',
            'Pueblo',
            'Ejido',
            'Aeropuerto',
            'Paraje',
            'Hacienda',
            'Conjunto habitacional',
            'Zona militar',
            'Puerto',
            'Zona federal',
            'Exhacienda',
            'Finca',
            'Campamento',
            'Zona naval'
        ];

        foreach ($tiposAsentamiento as $tipo) {
            DB::table('tipo_asentamiento_catalogo')->insert([
                'nombre' => $tipo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}