<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('domicilios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')->constrained('solicitantes');
            $table->enum('tipo', ['fiscal', 'fisico', 'notificaciones']);
            $table->string('calle', 255);
            $table->string('numero_exterior', 50)->nullable();
            $table->string('numero_interior', 50)->nullable();
            $table->string('colonia', 255);
            $table->string('localidad', 255)->nullable();
            $table->string('municipio', 255);
            $table->string('estado', 255);
            $table->string('pais', 255)->default('México');
            $table->string('codigo_postal', 10);
            $table->string('entre_calle_1', 255)->nullable();
            $table->string('entre_calle_2', 255)->nullable();
            $table->boolean('is_principal')->default(true);
            $table->timestamps();
            
            $table->index('solicitante_id');
            $table->index('codigo_postal');
            $table->index('municipio');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domicilios');
    }
};
