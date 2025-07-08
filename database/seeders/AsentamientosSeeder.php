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
    protected $chunkSize = 100; // Very small chunks for large files

    public function run()
    {
        $this->command->info('Creando asentamientos...');
        
        $jsonPath = public_path('json/asentamientos.json');
        if (!File::exists($jsonPath)) {
            $this->command->error('❌ Error al cargar asentamientos');
            return;
        }

        // Check file size and warn if too large
        $fileSize = File::size($jsonPath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        $this->command->info("📁 Tamaño del archivo: {$fileSizeMB}MB");

        if ($fileSizeMB > 10) {
            $this->command->warn("⚠️  Archivo muy grande ({$fileSizeMB}MB). Procesando en modo optimizado...");
        }

        try {
            // For very large files, use a more conservative approach
            if ($fileSizeMB > 20) {
                $this->processVeryLargeFile($jsonPath);
            } else {
                $this->processNormalFile($jsonPath);
            }
        } catch (\Exception $e) {
            $this->command->error('❌ Error al procesar asentamientos: ' . $e->getMessage());
            return;
        }

        $this->command->info('✅ Asentamientos creados exitosamente');
    }

    private function processVeryLargeFile($jsonPath)
    {
        $this->command->info('🔄 Procesando archivo muy grande...');
        
        // Read file in smaller chunks
        $content = File::get($jsonPath);
        
        // Try to decode the entire file
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
        }

        if (!isset($data['settlements'])) {
            throw new \Exception('No se encontró la clave "settlements" en el JSON');
        }

        $settlements = $data['settlements'];
        $totalSettlements = count($settlements);
        
        $this->command->info("📊 Total de asentamientos a procesar: {$totalSettlements}");

        // Process in very small chunks
        $processedCount = 0;
        $batchCount = 0;
        
        foreach (array_chunk($settlements, $this->chunkSize) as $chunk) {
            $this->insertSettlementsChunk($chunk);
            $processedCount += count($chunk);
            $batchCount++;
            
            // Show progress only every 500 batches (50,000 records)
            if ($batchCount % 500 === 0) {
                $percentage = round(($processedCount / $totalSettlements) * 100, 1);
                $this->command->info("📈 Progreso: {$processedCount}/{$totalSettlements} ({$percentage}%)");
            }
        }
        
        $this->command->info("📊 Total de asentamientos procesados: {$processedCount}");
    }

    private function processNormalFile($jsonPath)
    {
        $this->command->info('🔄 Procesando archivo normal...');
        
        $data = json_decode(File::get($jsonPath), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
        }

        if (!isset($data['settlements'])) {
            throw new \Exception('No se encontró la clave "settlements" en el JSON');
        }

        $settlements = $data['settlements'];
        $totalSettlements = count($settlements);
        
        $this->command->info("📊 Total de asentamientos a procesar: {$totalSettlements}");

        $processedCount = 0;
        $batchCount = 0;
        
        foreach (array_chunk($settlements, $this->chunkSize) as $chunk) {
            $this->insertSettlementsChunk($chunk);
            $processedCount += count($chunk);
            $batchCount++;
            
            // Show progress only every 100 batches (10,000 records)
            if ($batchCount % 100 === 0) {
                $percentage = round(($processedCount / $totalSettlements) * 100, 1);
                $this->command->info("📈 Progreso: {$processedCount}/{$totalSettlements} ({$percentage}%)");
            }
        }
        
        $this->command->info("📊 Total de asentamientos procesados: {$processedCount}");
    }

    private function insertSettlementsChunk($chunk)
    {
        $records = [];
        
        foreach ($chunk as $settlement) {
            if (!isset($settlement['name'], $settlement['zip_code'], $settlement['localidad_id'], $settlement['settlement_type_id'])) {
                continue; // Skip invalid records
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