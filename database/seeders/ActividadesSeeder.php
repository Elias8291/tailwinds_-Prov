<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ActividadesSeeder extends Seeder
{
    public function run()
    {
        // Primero intentar poblar con datos de DENUE
        $this->command->info('🚀 Intentando poblar actividades desde DENUE INEGI...');
        
        try {
            // Ejecutar el comando de población DENUE
            Artisan::call('denue:popular-actividades');
            $output = Artisan::output();
            
            $this->command->info('✅ Actividades pobladas desde DENUE exitosamente');
            $this->command->line($output);
            
            // Verificar si se poblaron datos
            $totalActividades = DB::table('actividad')->count();
            $totalSectores = DB::table('sector')->count();
            
            $this->command->info("📊 Resumen:");
            $this->command->line("   - Sectores creados: {$totalSectores}");
            $this->command->line("   - Actividades creadas: {$totalActividades}");
            
            if ($totalActividades > 0) {
                $this->command->info('🎉 Base de datos poblada exitosamente con datos DENUE');
                return;
            }
            
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Error al poblar desde DENUE: ' . $e->getMessage());
            $this->command->info('🔄 Intentando con datos locales de respaldo...');
        }
        
        // Respaldo: usar datos del JSON local si DENUE falla
        $this->poblarDesdeJsonLocal();
    }
    
    /**
     * Poblar desde archivo JSON local como respaldo
     */
    private function poblarDesdeJsonLocal()
    {
        try {
            $jsonPath = public_path('json/actividades.json');
            
            if (!File::exists($jsonPath)) {
                $this->command->error('❌ No se encontró el archivo de actividades locales');
                return;
            }
            
            $jsonData = File::get($jsonPath);
            $data = json_decode($jsonData, true);
            
            if (!isset($data['Hoja1']) || empty($data['Hoja1'])) {
                $this->command->error('❌ Formato de archivo JSON inválido');
                return;
            }
            
            $this->command->info('📁 Cargando actividades desde archivo local...');
            
            // Crear sectores básicos si no existen
            $this->crearSectoresBasicos();
            
            // Insertar actividades desde JSON
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
                $this->command->info('✅ ' . count($actividades) . ' actividades cargadas desde archivo local');
            }
            
        } catch (\Exception $e) {
            $this->command->error('❌ Error al cargar datos locales: ' . $e->getMessage());
        }
    }
    
    /**
     * Crear sectores básicos para el JSON local
     */
    private function crearSectoresBasicos()
    {
        $sectoresBasicos = [
            ['id' => 1, 'nombre' => 'Agricultura y ganadería', 'codigo' => '11', 'descripcion' => 'Sector primario'],
            ['id' => 2, 'nombre' => 'Comercio', 'codigo' => '43', 'descripcion' => 'Actividades comerciales'],
            ['id' => 3, 'nombre' => 'Servicios', 'codigo' => '81', 'descripcion' => 'Sector servicios'],
            ['id' => 4, 'nombre' => 'Industria', 'codigo' => '31', 'descripcion' => 'Sector industrial'],
            ['id' => 5, 'nombre' => 'Construcción', 'codigo' => '23', 'descripcion' => 'Sector construcción'],
        ];
        
        foreach ($sectoresBasicos as $sector) {
            DB::table('sector')->updateOrInsert(
                ['id' => $sector['id']],
                [
                    'nombre' => $sector['nombre'],
                    'codigo' => $sector['codigo'],
                    'descripcion' => $sector['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        $this->command->info('✅ Sectores básicos creados/actualizados');
    }
}