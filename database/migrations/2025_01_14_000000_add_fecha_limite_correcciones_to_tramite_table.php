<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaLimiteCorreccionesToTramiteTable extends Migration
{
    public function up()
    {
        Schema::table('tramite', function (Blueprint $table) {
            $table->timestamp('fecha_limite_correcciones')->nullable()->after('fecha_finalizacion');
        });
    }

    public function down()
    {
        Schema::table('tramite', function (Blueprint $table) {
            $table->dropColumn('fecha_limite_correcciones');
        });
    }
} 