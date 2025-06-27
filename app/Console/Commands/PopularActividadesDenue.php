<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Sector;
use App\Models\Actividad;

class PopularActividadesDenue extends Command
{
    protected $signature = 'denue:popular-actividades';
    protected $description = 'Poblar la base de datos con actividades económicas desde DENUE INEGI';

    public function handle()
    {
        $this->info('🚀 Iniciando población de actividades desde DENUE INEGI...');
        
        // Token de DENUE
        $DENUE_TOKEN = '64728524-5813-4d41-b564-515b12486442';
        
        $actividadesUnicas = collect();
        $sectoresUnicos = collect();
        $totalConsultas = 0;
        $totalRegistrosProcesados = 0;

        // Estrategia 1: Consulta general masiva
        $this->info('📋 Paso 1: Consulta general masiva...');
        try {
            $urlGeneral = "https://www.inegi.org.mx/app/api/denue/v1/consulta/BuscarEntidad/todos/00/1/1000/{$DENUE_TOKEN}";
            
            $this->line("🔍 Consultando: {$urlGeneral}");
            $dataGeneral = $this->consultarAPI($urlGeneral);
            
            if ($dataGeneral) {
                $this->info("✅ Consulta general: " . count($dataGeneral) . " establecimientos obtenidos");
                $this->procesarEstablecimientos($dataGeneral, $actividadesUnicas, $sectoresUnicos);
                $totalRegistrosProcesados += count($dataGeneral);
                $totalConsultas++;
                sleep(2);
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ Error en consulta general: " . $e->getMessage());
        }

        // Estrategia 2: Consultas por estados (todos los 32 estados)
        $this->info('📋 Paso 2: Consultas por estado...');
        $estados = [
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10',
            '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
            '21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
            '31', '32'
        ];

        $progressBar = $this->output->createProgressBar(count($estados));
        $progressBar->start();

        foreach ($estados as $estado) {
            try {
                $urlEstado = "https://www.inegi.org.mx/app/api/denue/v1/consulta/BuscarEntidad/todos/{$estado}/1/1000/{$DENUE_TOKEN}";
                $dataEstado = $this->consultarAPI($urlEstado);
                
                if ($dataEstado) {
                    $this->procesarEstablecimientos($dataEstado, $actividadesUnicas, $sectoresUnicos);
                    $totalRegistrosProcesados += count($dataEstado);
                    $totalConsultas++;
                }
                
                $progressBar->advance();
                sleep(1); // Pausa para no sobrecargar la API
                
            } catch (\Exception $e) {
                $this->warn("⚠️ Error en estado {$estado}: " . $e->getMessage());
                $progressBar->advance();
                continue;
            }
        }
        
        $progressBar->finish();
        $this->newLine();

        // Estrategia 3: Consultas específicas para obtener más variedad
        $this->info('📋 Paso 3: Consultas específicas...');
        $consultasEspecificas = [
            'comercio', 'servicios', 'restaurante', 'farmacia', 'tienda', 'consultorio',
            'escuela', 'taller', 'oficina', 'banco', 'hospital', 'hotel',
            'gasolinera', 'panaderia', 'carniceria', 'ferreteria', 'papeleria',
            'laboratorio', 'clinica', 'universidad', 'teatro', 'museo',
            'manufactura', 'construccion', 'transporte', 'tecnologia', 'consultoria'
        ];

        $progressBar2 = $this->output->createProgressBar(count($consultasEspecificas));
        $progressBar2->start();

        foreach ($consultasEspecificas as $consulta) {
            try {
                $urlEspecifica = "https://www.inegi.org.mx/app/api/denue/v1/consulta/BuscarEntidad/{$consulta}/00/1/1000/{$DENUE_TOKEN}";
                $dataEspecifica = $this->consultarAPI($urlEspecifica);
                
                if ($dataEspecifica) {
                    $this->procesarEstablecimientos($dataEspecifica, $actividadesUnicas, $sectoresUnicos);
                    $totalRegistrosProcesados += count($dataEspecifica);
                    $totalConsultas++;
                }
                
                $progressBar2->advance();
                sleep(0.5);
                
            } catch (\Exception $e) {
                $this->warn("⚠️ Error en consulta {$consulta}: " . $e->getMessage());
                $progressBar2->advance();
                continue;
            }
        }
        
        $progressBar2->finish();
        $this->newLine();

        // Mostrar estadísticas
        $this->info('📊 ESTADÍSTICAS DE CONSULTA:');
        $this->line("   Total consultas realizadas: {$totalConsultas}");
        $this->line("   Total establecimientos procesados: " . number_format($totalRegistrosProcesados));
        $this->line("   Actividades únicas encontradas: " . $actividadesUnicas->count());
        $this->line("   Sectores únicos encontrados: " . $sectoresUnicos->count());

        // Guardar en base de datos
        $this->info('💾 Guardando en base de datos...');
        $this->guardarEnBaseDatos($sectoresUnicos, $actividadesUnicas);

        $this->info('🎉 ¡Proceso completado exitosamente!');
        
        return Command::SUCCESS;
    }

    private function consultarAPI($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                throw new \Exception("HTTP Error: {$httpCode}");
            }
            
            $data = json_decode($response, true);
            
            if (!is_array($data)) {
                throw new \Exception("Formato de respuesta inválido");
            }
            
            return $data;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function procesarEstablecimientos($establecimientos, &$actividadesUnicas, &$sectoresUnicos)
    {
        foreach ($establecimientos as $establecimiento) {
            $claseActividad = $establecimiento['Clase_actividad'] ?? '';
            $clee = $establecimiento['CLEE'] ?? '';
            $ubicacion = $establecimiento['Ubicacion'] ?? '';
            
            if (empty($claseActividad)) continue;
            
            // Extraer código SCIAN del CLEE si está disponible
            $codigoSCIAN = '';
            if (!empty($clee) && is_numeric($clee)) {
                $codigoSCIAN = $clee;
            } else {
                // Generar código basado en el establecimiento ID
                $codigoSCIAN = $establecimiento['Id'] ?? substr(md5($claseActividad), 0, 6);
            }
            
            // Determinar sector basado en código SCIAN
            $sectorInfo = $this->determinarSector($codigoSCIAN, $ubicacion);
            
            // Añadir sector único
            if (!$sectoresUnicos->has($sectorInfo['codigo'])) {
                $sectoresUnicos->put($sectorInfo['codigo'], [
                    'codigo' => $sectorInfo['codigo'],
                    'nombre' => $sectorInfo['nombre'],
                    'descripcion' => $sectorInfo['descripcion']
                ]);
            }
            
            // Añadir actividad única
            $key = strtolower(trim($claseActividad));
            if (!$actividadesUnicas->has($key)) {
                $actividadesUnicas->put($key, [
                    'nombre' => $claseActividad,
                    'codigo_scian' => $codigoSCIAN,
                    'sector_codigo' => $sectorInfo['codigo'],
                    'ubicacion_ejemplo' => $ubicacion,
                    'fuente' => 'DENUE-INEGI'
                ]);
            }
        }
    }

    private function determinarSector($codigoSCIAN, $ubicacion)
    {
        // Extraer los primeros 2 dígitos para determinar el sector
        $codigoSector = is_numeric($codigoSCIAN) ? substr($codigoSCIAN, 0, 2) : '99';
        
        $sectores = [
            '11' => ['nombre' => 'Agricultura, ganadería, aprovechamiento forestal, pesca y caza', 'descripcion' => 'Sector primario'],
            '21' => ['nombre' => 'Minería', 'descripcion' => 'Extracción de recursos minerales'],
            '22' => ['nombre' => 'Electricidad, agua y gas', 'descripcion' => 'Servicios públicos'],
            '23' => ['nombre' => 'Construcción', 'descripcion' => 'Edificación e infraestructura'],
            '31' => ['nombre' => 'Industrias manufactureras', 'descripcion' => 'Transformación de materias primas'],
            '32' => ['nombre' => 'Industrias manufactureras', 'descripcion' => 'Transformación de materias primas'],
            '33' => ['nombre' => 'Industrias manufactureras', 'descripcion' => 'Transformación de materias primas'],
            '43' => ['nombre' => 'Comercio al por mayor', 'descripcion' => 'Venta en grandes volúmenes'],
            '46' => ['nombre' => 'Comercio al por menor', 'descripcion' => 'Venta directa al consumidor'],
            '48' => ['nombre' => 'Transportes, correos y almacenamiento', 'descripcion' => 'Servicios de transporte y logística'],
            '49' => ['nombre' => 'Transportes, correos y almacenamiento', 'descripcion' => 'Servicios de transporte y logística'],
            '51' => ['nombre' => 'Información en medios masivos', 'descripcion' => 'Medios de comunicación'],
            '52' => ['nombre' => 'Servicios financieros y de seguros', 'descripcion' => 'Servicios bancarios y financieros'],
            '53' => ['nombre' => 'Servicios inmobiliarios y de alquiler', 'descripcion' => 'Bienes raíces y alquiler'],
            '54' => ['nombre' => 'Servicios profesionales, científicos y técnicos', 'descripcion' => 'Consultoría y servicios especializados'],
            '55' => ['nombre' => 'Corporativos', 'descripcion' => 'Direcciones corporativas'],
            '56' => ['nombre' => 'Servicios de apoyo a los negocios', 'descripcion' => 'Servicios empresariales'],
            '61' => ['nombre' => 'Servicios educativos', 'descripcion' => 'Educación en todos los niveles'],
            '62' => ['nombre' => 'Servicios de salud y asistencia social', 'descripcion' => 'Atención médica y social'],
            '71' => ['nombre' => 'Servicios de esparcimiento y recreativos', 'descripcion' => 'Entretenimiento y cultura'],
            '72' => ['nombre' => 'Servicios de alojamiento y preparación de alimentos', 'descripcion' => 'Hoteles y restaurantes'],
            '81' => ['nombre' => 'Otros servicios', 'descripcion' => 'Servicios diversos'],
            '93' => ['nombre' => 'Actividades gubernamentales', 'descripcion' => 'Administración pública'],
        ];
        
        $sectorInfo = $sectores[$codigoSector] ?? [
            'nombre' => 'Actividades económicas diversas',
            'descripcion' => 'Actividades no clasificadas en otros sectores'
        ];
        
        return [
            'codigo' => $codigoSector,
            'nombre' => $sectorInfo['nombre'],
            'descripcion' => $sectorInfo['descripcion']
        ];
    }

    private function guardarEnBaseDatos($sectores, $actividades)
    {
        DB::beginTransaction();
        
        try {
            // Limpiar tablas existentes
            $this->info('🗑️ Limpiando datos existentes...');
            DB::table('actividad')->delete();
            DB::table('sector')->delete();
            
            // Insertar sectores
            $this->info('💾 Insertando sectores...');
            $sectoresArray = [];
            foreach ($sectores as $sector) {
                $sectoresArray[] = [
                    'codigo' => $sector['codigo'],
                    'nombre' => $sector['nombre'],
                    'descripcion' => $sector['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($sectoresArray)) {
                DB::table('sector')->insert($sectoresArray);
                $this->line("   ✅ {$sectores->count()} sectores insertados");
            }
            
            // Obtener IDs de sectores para relacionar con actividades
            $sectoresDB = DB::table('sector')->get()->keyBy('codigo');
            
            // Insertar actividades
            $this->info('💾 Insertando actividades...');
            $actividadesArray = [];
            foreach ($actividades as $actividad) {
                $sectorId = $sectoresDB->get($actividad['sector_codigo'])->id ?? null;
                
                if ($sectorId) {
                    $actividadesArray[] = [
                        'nombre' => $actividad['nombre'],
                        'codigo_scian' => $actividad['codigo_scian'],
                        'sector_id' => $sectorId,
                        'descripcion' => "Actividad registrada en DENUE: " . substr($actividad['ubicacion_ejemplo'], 0, 100),
                        'fuente' => $actividad['fuente'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            // Insertar en lotes para mejor rendimiento
            if (!empty($actividadesArray)) {
                foreach (array_chunk($actividadesArray, 1000) as $chunk) {
                    DB::table('actividad')->insert($chunk);
                }
                $this->line("   ✅ " . count($actividadesArray) . " actividades insertadas");
            }
            
            DB::commit();
            $this->info('✅ Datos guardados exitosamente en la base de datos');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error al guardar en base de datos: ' . $e->getMessage());
            throw $e;
        }
    }
} 