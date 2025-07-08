<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AsentamientosSeederOptimized extends Seeder
{
    /**
     * Tamaño del bloque para inserción por lotes
     * @var int
     */
    protected $chunkSize = 250; // Even smaller chunks for very large files

    public function run()
    {
        $this->command->info('Creando asentamientos (versión optimizada)...');
        
        $jsonPath = public_path('json/asentamientos.json');
        if (!File::exists($jsonPath)) {
            $this->command->error('❌ Error al cargar asentamientos');
            return;
        }

        // Check file size
        $fileSize = File::size($jsonPath);
        $fileSizeMB = round($fileSize / 1024 / 1024, 2);
        $this->command->info("📁 Tamaño del archivo: {$fileSizeMB}MB");

        try {
            // Use JSON streaming for very large files
            $this->streamProcessJson($jsonPath);
        } catch (\Exception $e) {
            $this->command->error('❌ Error al procesar asentamientos: ' . $e->getMessage());
            return;
        }

        $this->command->info('✅ Asentamientos creados exitosamente');
    }

    private function streamProcessJson($jsonPath)
    {
        $handle = fopen($jsonPath, 'r');
        if (!$handle) {
            throw new \Exception('No se pudo abrir el archivo JSON');
        }

        $totalProcessed = 0;
        $batchCount = 0;
        $currentBatch = [];
        $inSettlementsArray = false;
        $currentObject = '';
        $objectDepth = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            
            // Look for the start of settlements array
            if (strpos($line, '"settlements"') !== false && strpos($line, '[') !== false) {
                $inSettlementsArray = true;
                continue;
            }

            if (!$inSettlementsArray) {
                continue;
            }

            // Check if we're at the end of settlements array
            if ($inSettlementsArray && strpos($line, ']') !== false) {
                break;
            }

            // Process each line
            if (strpos($line, '{') !== false) {
                $objectDepth = 1;
                $currentObject = $line;
            } elseif ($objectDepth > 0) {
                $currentObject .= $line;
                
                // Count braces to track object boundaries
                $objectDepth += substr_count($line, '{') - substr_count($line, '}');
                
                if ($objectDepth === 0) {
                    // Complete object found, try to parse it
                    $this->processSettlementObject($currentObject, $currentBatch, $totalProcessed, $batchCount);
                    $currentObject = '';
                }
            }
        }

        // Process remaining batch
        if (!empty($currentBatch)) {
            $this->insertBatch($currentBatch);
            $totalProcessed += count($currentBatch);
        }

        fclose($handle);
        
        $this->command->info("📊 Total de asentamientos procesados: {$totalProcessed}");
    }

    private function processSettlementObject($jsonObject, &$currentBatch, &$totalProcessed, &$batchCount)
    {
        // Clean up the JSON object
        $jsonObject = trim($jsonObject, ",\n\r\t ");
        
        if (empty($jsonObject) || $jsonObject === '{' || $jsonObject === '}') {
            return;
        }

        try {
            $settlement = json_decode($jsonObject, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return; // Skip invalid JSON
            }

            if (!isset($settlement['name'], $settlement['zip_code'], $settlement['localidad_id'], $settlement['settlement_type_id'])) {
                return; // Skip incomplete records
            }

            $currentBatch[] = [
                'nombre' => $settlement['name'],
                'codigo_postal' => $settlement['zip_code'],
                'localidad_id' => $settlement['localidad_id'],
                'tipo_asentamiento_id' => $settlement['settlement_type_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert batch when it reaches the chunk size
            if (count($currentBatch) >= $this->chunkSize) {
                $this->insertBatch($currentBatch);
                $totalProcessed += count($currentBatch);
                $batchCount++;
                
                // Show progress every 10 batches
                if ($batchCount % 10 === 0) {
                    $this->command->info("📈 Procesados: {$totalProcessed} asentamientos (lote {$batchCount})");
                }
                
                $currentBatch = [];
            }

        } catch (\Exception $e) {
            // Skip problematic records
            return;
        }
    }

    private function insertBatch($batch)
    {
        if (!empty($batch)) {
            DB::table('asentamiento')->insert($batch);
        }
    }
} 