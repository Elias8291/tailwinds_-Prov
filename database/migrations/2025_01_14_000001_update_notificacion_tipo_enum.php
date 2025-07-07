<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateNotificacionTipoEnum extends Migration
{
    public function up()
    {
        // Usar SQL directo para modificar el ENUM
        DB::statement("ALTER TABLE notificacion MODIFY COLUMN tipo ENUM('Informativo', 'Advertencia', 'Error', 'aprobacion', 'correcciones', 'cita', 'revision') DEFAULT 'Informativo'");
    }

    public function down()
    {
        // Revertir al ENUM original
        DB::statement("ALTER TABLE notificacion MODIFY COLUMN tipo ENUM('Informativo', 'Advertencia', 'Error') DEFAULT 'Informativo'");
    }
} 