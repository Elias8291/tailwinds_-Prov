<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddParaCorreccionEstadoTramite extends Migration
{
    public function up()
    {
        // Usar SQL directo para modificar el ENUM
        DB::statement("ALTER TABLE tramite MODIFY COLUMN estado ENUM('Pendiente', 'En Revision', 'Aprobado', 'Rechazado', 'Por Cotejar', 'Para Corrección') DEFAULT 'Pendiente'");
    }

    public function down()
    {
        // Revertir al ENUM original
        DB::statement("ALTER TABLE tramite MODIFY COLUMN estado ENUM('Pendiente', 'En Revision', 'Aprobado', 'Rechazado', 'Por Cotejar') DEFAULT 'Pendiente'");
    }
} 