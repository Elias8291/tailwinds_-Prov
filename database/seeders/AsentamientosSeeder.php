<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AsentamientosSeeder extends Seeder
{
    /**
     * Tamaño del bloque para inserción por lotes
     * @var int
     */
    protected $chunkSize = 1000;

    public function run()
    {
        $this->command->info('Creando asentamientos...');
        
        $jsonPath = public_path('json/asentamientos.json');
        if (!File::exists($jsonPath)) {
            $this->command->error('❌ Error al cargar asentamientos');
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        if (!isset($data['settlements'])) {
            $this->command->error('❌ Error al cargar asentamientos');
            return;
        }

        foreach (array_chunk($data['settlements'], $this->chunkSize) as $chunk) {
            DB::table('asentamiento')->insert(
                collect($chunk)->map(function ($settlement) {
                    return [
                        'nombre' => $settlement['name'],
                        'codigo_postal' => $settlement['zip_code'],
                        'localidad_id' => $settlement['localidad_id'],
                        'tipo_asentamiento_id' => $settlement['settlement_type_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray()
            );
        }

        $this->command->info('✅ Asentamientos creados exitosamente');
    }
}