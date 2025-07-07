<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoVersionesTable extends Migration
{
    public function up(): void
    {
        Schema::create('documento_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_solicitante_id')->constrained('documento_solicitante')->onDelete('cascade');
            $table->string('ruta_archivo');
            $table->string('estado')->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->boolean('documento_cotejado')->default(false);
            $table->boolean('cotejo_presencial')->default(false);
            $table->text('observaciones_cotejo')->nullable();
            $table->dateTime('fecha_cotejo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento_versiones');
    }
} 