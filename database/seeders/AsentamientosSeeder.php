<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AsentamientosSeeder extends Seeder
{
    public function run()
    {
        if (!$data = json_decode(File::get(public_path('json/asentamientos.json')), true)) {
            return $this->command->error('Error al cargar asentamientos.json');
        }

        collect($data['settlements'] ?? [])->chunk(100)->each(function($chunk) {
            DB::table('asentamiento')->insert(
                $chunk->map(fn($item) => [
                    'nombre' => $item['name'],
                    'codigo_postal' => $item['zip_code'],
                    'localidad_id' => $item['localidad_id'],
                    'tipo_asentamiento_id' => $item['settlement_type_id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ])->filter()->toArray()
            );
        });
    }
}