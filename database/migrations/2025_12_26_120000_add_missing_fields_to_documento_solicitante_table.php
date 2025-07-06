<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToDocumentoSolicitanteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documento_solicitante', function (Blueprint $table) {
            // Verificar y agregar solo los campos que no existen
            if (!Schema::hasColumn('documento_solicitante', 'nombre_original')) {
                $table->string('nombre_original', 255)->nullable()->after('ruta_archivo');
            }
            if (!Schema::hasColumn('documento_solicitante', 'comentarios')) {
                $table->text('comentarios')->nullable()->after('observaciones');
            }
            if (!Schema::hasColumn('documento_solicitante', 'archivo')) {
                $table->longText('archivo')->nullable()->after('comentarios');
            }
            // documento_cotejado ya existe en otra migración, no lo agregamos
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documento_solicitante', function (Blueprint $table) {
            // Solo eliminar los campos que agregamos en esta migración
            $columnsToRemove = [];
            if (Schema::hasColumn('documento_solicitante', 'nombre_original')) {
                $columnsToRemove[] = 'nombre_original';
            }
            if (Schema::hasColumn('documento_solicitante', 'comentarios')) {
                $columnsToRemove[] = 'comentarios';
            }
            if (Schema::hasColumn('documento_solicitante', 'archivo')) {
                $columnsToRemove[] = 'archivo';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
} 