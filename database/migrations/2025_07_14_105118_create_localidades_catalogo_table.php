<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('localidades_catalogo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('municipio_id');
            $table->string('nombre', 370);
            $table->timestamps();

            $table->foreign('municipio_id')->references('id')->on('municipios_catalogo')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localidades_catalogo');
    }
}; 