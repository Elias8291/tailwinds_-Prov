<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Documento;
use App\Models\AI\AiTrainingData;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AITrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🤖 Iniciando seeder del módulo de entrenamiento de IA...');
        
        // Verificar que existan tipos de documentos
        $documentTypes = Documento::where('es_visible', true)->get();
        
        if ($documentTypes->isEmpty()) {
            $this->command->warn('⚠️  No se encontraron tipos de documentos. Creando algunos de ejemplo...');
            $this->createSampleDocumentTypes();
            $documentTypes = Documento::where('es_visible', true)->get();
        }
        
        // Obtener el primer usuario admin para las validaciones
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'Administrador');
        })->first();
        
        if (!$adminUser) {
            $adminUser = User::first();
        }
        
        if (!$adminUser) {
            $this->command->error('❌ No se encontraron usuarios. Por favor, ejecute primero UserSeeder.');
            return;
        }
        
        $this->command->info("👤 Usando usuario: {$adminUser->name} para las validaciones");
        
        // Crear datos de entrenamiento de ejemplo para cada tipo de documento
        foreach ($documentTypes as $docType) {
            $this->createTrainingDataForDocumentType($docType, $adminUser);
        }
        
        $this->command->info('✅ Seeder del módulo de entrenamiento de IA completado exitosamente!');
    }
    
    /**
     * Crear tipos de documento de ejemplo
     */
    private function createSampleDocumentTypes()
    {
        $documentTypes = [
            [
                'nombre' => 'Acta Constitutiva',
                'tipo_persona' => 'Moral',
                'descripcion' => 'Documento que acredita la constitución de una sociedad mercantil',
            ],
            [
                'nombre' => 'Identificación Oficial',
                'tipo_persona' => 'Física',
                'descripcion' => 'INE, pasaporte o cédula profesional vigente',
            ],
            [
                'nombre' => 'Comprobante de Domicilio',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Recibo de servicios públicos no mayor a 3 meses',
            ],
            [
                'nombre' => 'RFC',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Cédula de identificación fiscal',
            ],
            [
                'nombre' => 'Poder Notarial',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Documento que otorga representación legal',
            ],
        ];
        
        foreach ($documentTypes as $docData) {
            Documento::create($docData);
            $this->command->info("📄 Creado tipo de documento: {$docData['nombre']}");
        }
    }
    
    /**
     * Crear datos de entrenamiento para un tipo de documento específico
     */
    private function createTrainingDataForDocumentType(Documento $docType, User $adminUser)
    {
        $this->command->info("📚 Creando datos de entrenamiento para: {$docType->nombre}");
        
        // Simular diferentes estados de validación
        $statuses = [
            'validated' => 15,    // 15 documentos validados
            'pending' => 5,       // 5 documentos pendientes
            'rejected' => 2,      // 2 documentos rechazados
        ];
        
        foreach ($statuses as $status => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $this->createTrainingDataRecord($docType, $adminUser, $status, $i);
            }
        }
    }
    
    /**
     * Crear un registro individual de datos de entrenamiento
     */
    private function createTrainingDataRecord(Documento $docType, User $adminUser, string $status, int $index)
    {
        // Generar nombres de archivos realistas
        $fileExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        $extension = $fileExtensions[array_rand($fileExtensions)];
        $fileName = strtolower(str_replace(' ', '_', $docType->nombre)) . "_{$index}.{$extension}";
        $filePath = "ai-training/{$docType->id}/{$fileName}";
        
        // Texto extraído simulado basado en el tipo de documento
        $extractedText = $this->generateSampleText($docType->nombre, $index);
        
        // Características del documento simuladas
        $features = [
            'page_count' => rand(1, 5),
            'word_count' => rand(100, 1000),
            'has_signature' => rand(0, 1) == 1,
            'has_seal' => rand(0, 1) == 1,
            'text_confidence' => rand(80, 99) / 100,
            'layout_structure' => ['header', 'body', 'footer'],
        ];
        
        // Metadatos
        $metadata = [
            'original_name' => $fileName,
            'uploaded_at' => now()->subDays(rand(1, 30))->toISOString(),
            'uploaded_by' => $adminUser->id,
            'description' => "Documento de ejemplo para entrenamiento - {$docType->nombre}",
            'file_hash' => hash('sha256', $fileName . time()),
        ];
        
        $trainingData = [
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $extension === 'pdf' ? 'application/pdf' : "image/{$extension}",
            'file_size' => rand(100000, 5000000), // 100KB a 5MB
            'expected_document_type' => $docType->id,
            'validation_status' => $status,
            'extracted_text' => $extractedText,
            'document_features' => $features,
            'metadata' => $metadata,
        ];
        
        // Si está validado o rechazado, agregar información del validador
        if ($status !== 'pending') {
            $trainingData['validated_by'] = $adminUser->id;
            $trainingData['validated_at'] = now()->subDays(rand(1, 15));
            
            if ($status === 'rejected') {
                $trainingData['validation_notes'] = 'Documento rechazado: ' . $this->getRandomRejectionReason();
            } else {
                $trainingData['validation_notes'] = 'Documento validado correctamente para entrenamiento';
            }
        }
        
        AiTrainingData::create($trainingData);
    }
    
    /**
     * Generar texto de ejemplo para diferentes tipos de documentos
     */
    private function generateSampleText(string $documentType, int $index): string
    {
        $sampleTexts = [
            'Acta Constitutiva' => [
                'ESCRITURA PÚBLICA NÚMERO ' . (1000 + $index) . ' CONSTITUCIÓN DE SOCIEDAD ANÓNIMA DE CAPITAL VARIABLE. En la Ciudad de México, siendo las 10:00 horas del día ' . date('d') . ' de enero de 2024, ante mí, Licenciado Juan Pérez Notario Público...',
                'ACTA CONSTITUTIVA DE EMPRESA EJEMPLO S.A. DE C.V. Los comparecientes manifiestan su voluntad de constituir una Sociedad Anónima de Capital Variable...',
            ],
            'Identificación Oficial' => [
                'INSTITUTO NACIONAL ELECTORAL CREDENCIAL PARA VOTAR NOMBRE: JUAN PÉREZ MARTÍNEZ CLAVE DE ELECTOR: PEMJ850101H' . sprintf('%03d', $index) . ' FECHA DE NACIMIENTO: 01/01/1985',
                'INE MÉXICO CREDENCIAL PARA VOTAR CON FOTOGRAFÍA NOMBRE DEL CIUDADANO: MARÍA GONZÁLEZ LÓPEZ CURP: GOLM900215M' . sprintf('%03d', $index),
            ],
            'Comprobante de Domicilio' => [
                'COMISIÓN FEDERAL DE ELECTRICIDAD RECIBO DE ENERGÍA ELÉCTRICA NOMBRE: JUAN PÉREZ MARTÍNEZ SERVICIO: 123456789 PERIODO: ENERO 2024 IMPORTE: $450.00',
                'TELMEX RECIBO TELEFÓNICO CLIENTE: MARÍA GONZÁLEZ PERÍODO: DICIEMBRE 2023 TOTAL A PAGAR: $320.00',
            ],
            'RFC' => [
                'SERVICIO DE ADMINISTRACIÓN TRIBUTARIA CÉDULA DE IDENTIFICACIÓN FISCAL RFC: PEMJ850101H' . sprintf('%02d', $index) . ' NOMBRE: JUAN PÉREZ MARTÍNEZ',
                'SAT REGISTRO FEDERAL DE CONTRIBUYENTES RAZÓN SOCIAL: EMPRESA EJEMPLO S.A. DE C.V. RFC: EEJ240101A' . sprintf('%02d', $index),
            ],
            'Poder Notarial' => [
                'PODER NOTARIAL ESPECIAL Otorgo poder especial amplio y suficiente al Licenciado Juan Pérez para que en mi nombre y representación...',
                'ESCRITURA PÚBLICA DE PODER GENERAL Los comparecientes otorgan poder general para actos de administración y pleitos y cobranzas...',
            ],
        ];
        
        $texts = $sampleTexts[$documentType] ?? ['Documento de ejemplo para entrenamiento del sistema de IA.'];
        return $texts[array_rand($texts)];
    }
    
    /**
     * Obtener una razón aleatoria de rechazo
     */
    private function getRandomRejectionReason(): string
    {
        $reasons = [
            'Calidad de imagen insuficiente para el entrenamiento',
            'Documento no corresponde al tipo seleccionado',
            'Archivo corrupto o ilegible',
            'Documento incompleto o parcialmente visible',
            'Formato de archivo no compatible',
            'Texto no legible por baja resolución',
        ];
        
        return $reasons[array_rand($reasons)];
    }
} 