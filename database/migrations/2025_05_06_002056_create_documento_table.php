<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoTable extends Migration
{
    public function up()
    {
        Schema::create('documento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->enum('tipo_persona', ['Física', 'Moral', 'Ambas']);
            $table->enum('seccion', [
                'ninguna',
                'uno', 
                'dos', 
                'tres', 
                'cuatro', 
                'cinco', 
                'seis',
                '1',
                '2', 
                '3', 
                '4', 
                '5', 
                '6',
                'datos_generales',
                'domicilio',
                'documentos',
                'actividades',
                'accionistas',
                'finalizacion'
            ])->default('ninguna');
            $table->text('descripcion')->nullable();
            $table->boolean('es_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento');
    }
}