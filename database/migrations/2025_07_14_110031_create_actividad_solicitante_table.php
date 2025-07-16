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
        Schema::create('actividad_solicitante', function (Blueprint $table) {
            $table->foreignId('solicitante_id')->constrained('solicitantes');
            $table->foreignId('actividad_id')->constrained('actividades_catalogo');
            $table->timestamps();
            
            $table->primary(['solicitante_id', 'actividad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividad_solicitante');
    }
};
