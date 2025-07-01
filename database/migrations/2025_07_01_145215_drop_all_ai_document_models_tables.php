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
        // Lista completa de tablas de IA que necesitamos eliminar
        $aiTables = [
            'ai_document_models',
            'ai_training_data',
            'ai_validation_results',
            'ai_analysis_results', 
            'ai_document_analysis',
            'ai_predictions',
            'ai_training_history',
            'ai_model_results',
            'ai_classification_results',
            'document_analysis_log',
            'ai_extracted_data',
            'ai_processing_queue'
        ];

        // Tablas principales que NO debemos tocar (referencias legítimas)
        $legitimateTables = ['tramite', 'documento', 'documento_solicitante', 'seccion_revision', 'users'];

        echo "🧹 Iniciando limpieza completa de tablas de IA...\n";

        // Paso 1: Eliminar todas las restricciones de clave foránea de tablas de IA
        foreach ($aiTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                try {
                    // Buscar TODAS las claves foráneas de la tabla
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = ? 
                        AND CONSTRAINT_NAME LIKE '%_foreign'
                        AND TABLE_SCHEMA = DATABASE()
                    ", [$tableName]);
                    
                    foreach ($foreignKeys as $fk) {
                        try {
                            DB::statement("ALTER TABLE {$tableName} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                            echo "✅ Eliminada clave foránea: {$fk->CONSTRAINT_NAME} de tabla {$tableName}\n";
                        } catch (\Exception $e) {
                            echo "⚠️ No se pudo eliminar {$fk->CONSTRAINT_NAME}: " . $e->getMessage() . "\n";
                        }
                    }
                } catch (\Exception $e) {
                    echo "⚠️ Error buscando claves foráneas en {$tableName}: " . $e->getMessage() . "\n";
                }
            }
        }

        // Paso 2: Buscar y eliminar restricciones hacia documento/documento_solicitante desde tablas no legítimas
        try {
            $problematicForeignKeys = DB::select("
                SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE REFERENCED_TABLE_NAME IN ('documento', 'documento_solicitante')
                AND TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME NOT IN ('" . implode("','", $legitimateTables) . "')
            ");
            
            foreach ($problematicForeignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE {$fk->TABLE_NAME} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                    echo "✅ Eliminada referencia problemática: {$fk->CONSTRAINT_NAME} de {$fk->TABLE_NAME} hacia {$fk->REFERENCED_TABLE_NAME}\n";
                } catch (\Exception $e) {
                    echo "⚠️ No se pudo eliminar referencia {$fk->CONSTRAINT_NAME}: " . $e->getMessage() . "\n";
                }
            }
        } catch (\Exception $e) {
            echo "⚠️ Error buscando referencias problemáticas: " . $e->getMessage() . "\n";
        }

        // Paso 3: Eliminar todas las tablas de IA
        foreach ($aiTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                try {
                    Schema::dropIfExists($tableName);
                    echo "🗑️ Eliminada tabla: {$tableName}\n";
                } catch (\Exception $e) {
                    echo "⚠️ Error eliminando tabla {$tableName}: " . $e->getMessage() . "\n";
                }
            }
        }

        // Paso 4: Buscar y eliminar cualquier tabla restante que tenga 'ai_' en el nombre
        try {
            $remainingAiTables = DB::select("
                SELECT TABLE_NAME 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME LIKE 'ai_%'
            ");
            
            foreach ($remainingAiTables as $table) {
                try {
                    // Primero eliminar todas las claves foráneas
                    $fks = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = ? 
                        AND CONSTRAINT_NAME LIKE '%_foreign'
                        AND TABLE_SCHEMA = DATABASE()
                    ", [$table->TABLE_NAME]);
                    
                    foreach ($fks as $fk) {
                        try {
                            DB::statement("ALTER TABLE {$table->TABLE_NAME} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                        } catch (\Exception $e) {
                            // Continuar
                        }
                    }
                    
                    // Luego eliminar la tabla
                    Schema::dropIfExists($table->TABLE_NAME);
                    echo "🗑️ Eliminada tabla AI restante: {$table->TABLE_NAME}\n";
                } catch (\Exception $e) {
                    echo "⚠️ Error eliminando tabla AI restante {$table->TABLE_NAME}: " . $e->getMessage() . "\n";
                }
            }
        } catch (\Exception $e) {
            echo "⚠️ Error buscando tablas AI restantes: " . $e->getMessage() . "\n";
        }

        echo "🎉 Limpieza completa de tablas de IA finalizada!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No recreamos las tablas de IA ya que no eran parte del proyecto original
        echo "Las tablas de IA no se recrearán automáticamente\n";
    }
};
