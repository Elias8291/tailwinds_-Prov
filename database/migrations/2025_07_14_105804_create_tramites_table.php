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
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')->constrained('solicitantes');
            $table->enum('tipo', ['inscripcion', 'renovacion', 'actualizacion']);
            $table->enum('estado', ['pendiente', 'Por Cotejar', 'Aprobado', 'Cancelado', 'Rechazado'])->default('pendiente');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_finalizacion')->nullable();
            $table->date('fecha_limite_correcciones')->nullable();
            $table->timestamps();
            
            $table->index('solicitante_id');
            $table->index('tipo');
            $table->index('estado')->comment('Índice crucial para que los revisores encuentren trabajo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramites');
    }
};
