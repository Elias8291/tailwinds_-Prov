<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateNotificacionTipoEnum extends Migration
{
    public function up()
    {
        // Primero convertimos la columna a VARCHAR para no perder datos
        DB::statement("ALTER TABLE notificacion MODIFY COLUMN tipo VARCHAR(255) DEFAULT 'Informativo'");
        
        // Actualizamos cualquier valor que no coincida con los nuevos valores permitidos
        DB::table('notificacion')
            ->whereNotIn('tipo', ['Informativo', 'Advertencia', 'Error', 'aprobacion', 'correcciones', 'cita', 'revision'])
            ->update(['tipo' => 'Informativo']);

        // Finalmente convertimos a ENUM con los nuevos valores
        DB::statement("ALTER TABLE notificacion MODIFY COLUMN tipo ENUM('Informativo', 'Advertencia', 'Error', 'aprobacion', 'correcciones', 'cita', 'revision') DEFAULT 'Informativo'");
    }

    public function down()
    {
        // Primero convertimos a VARCHAR para no perder datos
        DB::statement("ALTER TABLE notificacion MODIFY COLUMN tipo VARCHAR(255) DEFAULT 'Informativo'");
        
        // Actualizamos cualquier valor que no coincida con los valores originales
        DB::table('notificacion')
            ->whereNotIn('tipo', ['Informativo', 'Advertencia', 'Error'])
            ->update(['tipo' => 'Informativo']);

        // Revertimos al ENUM original
        DB::statement("ALTER TABLE notificacion MODIFY COLUMN tipo ENUM('Informativo', 'Advertencia', 'Error') DEFAULT 'Informativo'");
    }
} 