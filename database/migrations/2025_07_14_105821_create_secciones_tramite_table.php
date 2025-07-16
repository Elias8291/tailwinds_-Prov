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
        Schema::create('secciones_tramite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites');
            $table->foreignId('seccion_id')->constrained('secciones_catalogo');
            $table->enum('estado', ['pendiente', 'completado', 'en_revision', 'con_observaciones'])->default('pendiente');
            $table->timestamps();
            
            $table->unique(['tramite_id', 'seccion_id']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones_tramite');
    }
};
