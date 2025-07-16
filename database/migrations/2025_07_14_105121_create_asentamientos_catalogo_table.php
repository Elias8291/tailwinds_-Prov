<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asentamientos_catalogo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 405);
            $table->string('codigo_postal', 5);
            $table->unsignedBigInteger('localidad_id');
            $table->unsignedBigInteger('tipo_asentamiento_id');
            $table->timestamps();

            $table->foreign('localidad_id')->references('id')->on('localidades_catalogo')->onDelete('cascade');
            $table->foreign('tipo_asentamiento_id')->references('id')->on('tipo_asentamiento_catalogo')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asentamientos_catalogo');
    }
}; 