<?php

namespace Database\Seeders;

use App\Models\AI\AiDocumentModel;
use App\Models\AI\AiTrainingData;
use App\Models\Documento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AIModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🤖 Creando modelo inicial de IA...');

        // Crear modelo de ejemplo
        $model = AiDocumentModel::create([
            'name' => 'Modelo Básico de Documentos',
            'version' => '1.0.0',
            'description' => 'Modelo inicial para reconocimiento básico de tipos de documentos',
            'supported_document_types' => [
                'Constancia de Situación Fiscal',
                'Acta de Nacimiento',
                'Credencial de Elector',
                'Comprobante de Domicilio',
                'CURP',
                'RFC'
            ],
            'model_path' => 'ai_models/basic_model.json',
            'training_parameters' => [
                'algorithm' => 'rule_based_enhanced',
                'feature_extraction' => 'text_and_metadata',
                'validation_split' => 0.2,
                'created_by_seeder' => true,
            ],
            'accuracy' => 0.85,
            'training_samples_count' => 50,
            'status' => 'ready',
            'is_active' => true,
            'training_started_at' => now()->subHours(2),
            'training_completed_at' => now()->subHour(1),
        ]);

        // Crear configuración del modelo
        $modelConfig = [
            'model_id' => $model->id,
            'version' => $model->version,
            'training_date' => now()->toISOString(),
            'supported_types' => $model->supported_document_types,
            'training_data_count' => 50,
            'rules' => [
                'Constancia de Situación Fiscal' => [
                    'keywords' => ['constancia', 'fiscal', 'situación', 'sat', 'contribuyente'],
                    'patterns' => ['rfc', 'cédula', 'actividad'],
                    'metadata_rules' => ['contains_official_format']
                ],
                'Acta de Nacimiento' => [
                    'keywords' => ['acta', 'nacimiento', 'registro', 'civil', 'nació'],
                    'patterns' => ['curp', 'lugar_nacimiento', 'fecha_nacimiento'],
                    'metadata_rules' => ['official_document']
                ],
                'Credencial de Elector' => [
                    'keywords' => ['ine', 'credencial', 'elector', 'electoral', 'instituto'],
                    'patterns' => ['clave_elector', 'vigencia', 'sección'],
                    'metadata_rules' => ['government_id']
                ],
                'Comprobante de Domicilio' => [
                    'keywords' => ['comprobante', 'domicilio', 'dirección', 'factura', 'recibo'],
                    'patterns' => ['servicios', 'agua', 'luz', 'teléfono', 'gas'],
                    'metadata_rules' => ['utility_bill']
                ],
                'CURP' => [
                    'keywords' => ['curp', 'población', 'registro', 'clave', 'única'],
                    'patterns' => ['18_characters', 'alphanumeric'],
                    'metadata_rules' => ['government_document']
                ],
                'RFC' => [
                    'keywords' => ['rfc', 'federal', 'contribuyentes', 'registro', 'hacienda'],
                    'patterns' => ['tax_id', '12_or_13_characters'],
                    'metadata_rules' => ['tax_document']
                ]
            ],
            'confidence_thresholds' => [
                'high' => 0.9,
                'medium' => 0.7,
                'low' => 0.5
            ]
        ];

        // Guardar configuración del modelo
        Storage::put($model->model_path, json_encode($modelConfig, JSON_PRETTY_PRINT));

        $this->command->info("✅ Modelo creado: {$model->name} (ID: {$model->id})");
        $this->command->info("📊 Soporta " . count($model->supported_document_types) . " tipos de documentos");
        $this->command->info("🎯 Modelo activado como predeterminado");

        // Crear algunos datos de entrenamiento de ejemplo (simulados)
        $this->createSampleTrainingData();

        $this->command->info('🚀 Módulo de IA configurado correctamente');
    }

    /**
     * Crear datos de entrenamiento de ejemplo
     */
    private function createSampleTrainingData(): void
    {
        $this->command->info('📚 Creando datos de entrenamiento de ejemplo...');

        $documentTypes = [
            'Constancia de Situación Fiscal',
            'Acta de Nacimiento',
            'Credencial de Elector',
            'Comprobante de Domicilio'
        ];

        foreach ($documentTypes as $type) {
            // Crear algunos registros de datos de entrenamiento simulados
            for ($i = 1; $i <= 5; $i++) {
                AiTrainingData::create([
                    'documento_id' => 1, // Suponiendo que existe un documento con ID 1
                    'file_path' => "ai_training_data/sample_{$type}_{$i}.pdf",
                    'file_hash' => md5("sample_{$type}_{$i}_" . time()),
                    'document_type' => $type,
                    'extracted_features' => [
                        'text_content' => "Contenido simulado para {$type}",
                        'layout_features' => [
                            'page_count' => rand(1, 3),
                            'file_size' => rand(100000, 500000),
                            'has_images' => rand(0, 1) === 1,
                            'text_density' => rand(500, 2000) / 10000
                        ],
                        'metadata' => [
                            'mime_type' => 'application/pdf',
                            'extension' => 'pdf',
                            'size_mb' => rand(1, 5)
                        ],
                        'text_features' => [
                            'length' => rand(500, 2000),
                            'word_count' => rand(100, 400),
                            'has_numbers' => true,
                            'has_dates' => true,
                            'keywords' => $this->getKeywordsForType($type)
                        ]
                    ],
                    'confidence_score' => rand(8000, 9500) / 10000, // 0.8 - 0.95
                    'is_validated' => true,
                    'is_used_for_training' => true,
                    'validation_count' => 1,
                    'validation_data' => [
                        'validated_by' => 1,
                        'is_correct' => true,
                        'validated_at' => now(),
                        'notes' => 'Datos de ejemplo creados por seeder'
                    ]
                ]);
            }
        }

        $this->command->info('✅ Creados ' . (count($documentTypes) * 5) . ' registros de entrenamiento de ejemplo');
    }

    /**
     * Obtener palabras clave para un tipo de documento
     */
    private function getKeywordsForType(string $type): array
    {
        $keywords = [
            'Constancia de Situación Fiscal' => ['constancia', 'fiscal', 'sat', 'contribuyente', 'actividad'],
            'Acta de Nacimiento' => ['acta', 'nacimiento', 'registro', 'civil', 'nació'],
            'Credencial de Elector' => ['ine', 'credencial', 'elector', 'electoral', 'instituto'],
            'Comprobante de Domicilio' => ['comprobante', 'domicilio', 'factura', 'servicios', 'recibo']
        ];

        return $keywords[$type] ?? ['documento', 'oficial'];
    }
} 