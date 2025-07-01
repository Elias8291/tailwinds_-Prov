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
        // Verificar si la tabla ai_training_data existe
        if (Schema::hasTable('ai_training_data')) {
            // Intentar eliminar la restricción de clave foránea
            try {
                Schema::table('ai_training_data', function (Blueprint $table) {
                    $table->dropForeign('ai_training_data_documento_solicitante_id_foreign');
                });
            } catch (\Exception $e) {
                // Si la restricción no existe con ese nombre, intentar eliminarla de otra forma
                try {
                    DB::statement('ALTER TABLE ai_training_data DROP FOREIGN KEY ai_training_data_documento_solicitante_id_foreign');
                } catch (\Exception $e2) {
                    // Intentar encontrar y eliminar cualquier clave foránea que referencie documento_solicitante
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = 'ai_training_data' 
                        AND REFERENCED_TABLE_NAME = 'documento_solicitante'
                        AND TABLE_SCHEMA = DATABASE()
                    ");
                    
                    foreach ($foreignKeys as $fk) {
                        try {
                            DB::statement("ALTER TABLE ai_training_data DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                        } catch (\Exception $e3) {
                            // Continuar con la siguiente clave foránea
                        }
                    }
                }
            }
            
            // Eliminar la tabla ai_training_data completamente
            Schema::dropIfExists('ai_training_data');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No recreamos la tabla ai_training_data ya que no era parte del proyecto original
        // Si necesita ser recreada, deberá hacerse manualmente
    }
};
