<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ActividadesSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Cargando actividades...');
        
        $jsonPath = public_path('json/denue/actividades_denue.json');
        
        if (File::exists($jsonPath)) {
            $this->poblarDesdeDenueJson($jsonPath);
        } else {
            $this->poblarDesdeJsonLocal();
        }
    }
    
    private function poblarDesdeDenueJson($jsonPath)
    {
        try {
            $jsonData = File::get($jsonPath);
            $data = json_decode($jsonData, true);
            
            if (!isset($data['sectores']) || !isset($data['actividades'])) {
                throw new \Exception('Formato de archivo JSON inválido');
            }
            
            DB::beginTransaction();
            
            // Limpiar e insertar sectores
            DB::table('actividad')->delete();
            DB::table('sector')->delete();
            
            foreach ($data['sectores'] as $sector) {
                DB::table('sector')->insert([
                    'codigo' => $sector['codigo'],
                    'nombre' => $sector['nombre'],
                    'descripcion' => $sector['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Obtener IDs de sectores e insertar actividades
            $sectoresDB = DB::table('sector')->get()->keyBy('codigo');
            $actividadesLote = [];
            
            foreach ($data['actividades'] as $actividad) {
                $sectorId = $sectoresDB->get($actividad['sector_codigo'])->id ?? null;
                
                if ($sectorId) {
                    $actividadesLote[] = [
                        'nombre' => $actividad['nombre'],
                        'codigo_scian' => $actividad['codigo_scian'],
                        'sector_id' => $sectorId,
                        'descripcion' => $actividad['descripcion'],
                        'fuente' => $actividad['fuente'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (count($actividadesLote) >= 1000) {
                    DB::table('actividad')->insert($actividadesLote);
                    $actividadesLote = [];
                }
            }
            
            if (!empty($actividadesLote)) {
                DB::table('actividad')->insert($actividadesLote);
            }
            
            DB::commit();
            $this->command->info('✅ Actividades cargadas exitosamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->poblarDesdeJsonLocal();
        }
    }
    
    private function poblarDesdeJsonLocal()
    {
        try {
            $jsonPath = public_path('json/actividades.json');
            
            if (!File::exists($jsonPath)) {
                $this->command->error('❌ No se encontró el archivo de actividades');
                return;
            }
            
            $data = json_decode(File::get($jsonPath), true);
            
            if (!isset($data['Hoja1']) || empty($data['Hoja1'])) {
                $this->command->error('❌ Formato de archivo JSON inválido');
                return;
            }
            
            $this->crearSectoresBasicos();
            
            $actividades = [];
            foreach ($data['Hoja1'] as $item) {
                $actividades[] = [
                    'nombre' => $item['actividad'],
                    'sector_id' => $item['id_sector'],
                    'codigo_scian' => null,
                    'descripcion' => 'Actividad del catálogo local',
                    'fuente' => 'JSON_LOCAL',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($actividades)) {
                DB::table('actividad')->insert($actividades);
                $this->command->info('✅ Actividades cargadas exitosamente');
            }
            
        } catch (\Exception $e) {
            $this->command->error('❌ Error al cargar actividades');
        }
    }
    
    private function crearSectoresBasicos()
    {
        $sectores = [
            ['id' => 1, 'nombre' => 'Agricultura y ganadería', 'codigo' => '11'],
            ['id' => 2, 'nombre' => 'Comercio', 'codigo' => '43'],
            ['id' => 3, 'nombre' => 'Servicios', 'codigo' => '81'],
            ['id' => 4, 'nombre' => 'Industria', 'codigo' => '31'],
            ['id' => 5, 'nombre' => 'Construcción', 'codigo' => '23'],
        ];

        DB::table('sector')->insertOrIgnore(
            collect($sectores)->map(function ($sector) {
                return array_merge($sector, [
                    'descripcion' => 'Sector económico',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })->toArray()
        );
    }
}