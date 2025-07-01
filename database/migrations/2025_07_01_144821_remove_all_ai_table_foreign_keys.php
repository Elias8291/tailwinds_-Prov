<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Lista de posibles tablas de IA que podrían tener referencias a documento_solicitante
        $aiTables = [
            'ai_training_data',
            'ai_validation_results',
            'ai_analysis_results', 
            'ai_document_analysis',
            'ai_predictions',
            'ai_training_history',
            'ai_model_results',
            'ai_classification_results'
        ];

        foreach ($aiTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                try {
                    // Buscar todas las claves foráneas que referencien documento_solicitante
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = ? 
                        AND REFERENCED_TABLE_NAME = 'documento_solicitante'
                        AND TABLE_SCHEMA = DATABASE()
                    ", [$tableName]);
                    
                    // Eliminar cada clave foránea encontrada
                    foreach ($foreignKeys as $fk) {
                        try {
                            DB::statement("ALTER TABLE {$tableName} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                            echo "Eliminada clave foránea: {$fk->CONSTRAINT_NAME} de tabla {$tableName}\n";
                        } catch (\Exception $e) {
                            echo "No se pudo eliminar la clave foránea {$fk->CONSTRAINT_NAME}: " . $e->getMessage() . "\n";
                        }
                    }
                    
                    // Eliminar la tabla completa
                    Schema::dropIfExists($tableName);
                    echo "Eliminada tabla: {$tableName}\n";
                    
                } catch (\Exception $e) {
                    echo "Error procesando tabla {$tableName}: " . $e->getMessage() . "\n";
                }
            }
        }

        // Verificar si quedan otras restricciones hacia documento_solicitante
        try {
            $remainingForeignKeys = DB::select("
                SELECT TABLE_NAME, CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE REFERENCED_TABLE_NAME = 'documento_solicitante'
                AND TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME NOT IN ('tramite', 'documento', 'seccion_revision')
            ");
            
            foreach ($remainingForeignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE {$fk->TABLE_NAME} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                    echo "Eliminada clave foránea restante: {$fk->CONSTRAINT_NAME} de tabla {$fk->TABLE_NAME}\n";
                } catch (\Exception $e) {
                    echo "No se pudo eliminar la clave foránea restante {$fk->CONSTRAINT_NAME}: " . $e->getMessage() . "\n";
                }
            }
        } catch (\Exception $e) {
            echo "Error buscando claves foráneas restantes: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No recreamos las tablas de IA ya que no eran parte del proyecto original
        // Si necesitan ser recreadas, deberá hacerse manualmente
    }
};
