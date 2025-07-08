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
    protected $chunkSize = 100;

    public function run()
    {
        $this->command->info('Creando asentamientos...');
        
        $jsonPath = public_path('json/asentamientos.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('Archivo asentamientos.json no encontrado');
            return;
        }

        try {
            $this->processFile($jsonPath);
            $this->command->info('Asentamientos creados exitosamente');
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }

    private function processFile($jsonPath)
    {
        $data = json_decode(File::get($jsonPath), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON inválido');
        }

        if (!isset($data['settlements'])) {
            throw new \Exception('Estructura JSON inválida');
        }

        $settlements = $data['settlements'];
        $total = count($settlements);
        
        $this->command->info("Procesando {$total} asentamientos...");

        $processed = 0;
        $batchCount = 0;
        
        foreach (array_chunk($settlements, $this->chunkSize) as $chunk) {
            $this->insertChunk($chunk);
            $processed += count($chunk);
            $batchCount++;
            
            if ($batchCount % 1000 === 0) {
                $percentage = round(($processed / $total) * 100, 1);
                $this->command->info("{$processed}/{$total} ({$percentage}%)");
            }
        }
    }

    private function insertChunk($chunk)
    {
        $records = [];
        
        foreach ($chunk as $settlement) {
            if (!isset($settlement['name'], $settlement['zip_code'], $settlement['localidad_id'], $settlement['settlement_type_id'])) {
                continue;
            }

            $records[] = [
                'nombre' => $settlement['name'],
                'codigo_postal' => $settlement['zip_code'],
                'localidad_id' => $settlement['localidad_id'],
                'tipo_asentamiento_id' => $settlement['settlement_type_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($records)) {
            DB::table('asentamiento')->insert($records);
        }
    }
}