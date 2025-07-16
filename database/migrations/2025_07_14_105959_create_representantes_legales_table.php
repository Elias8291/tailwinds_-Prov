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
        Schema::create('representantes_legales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')->constrained('solicitantes');
            $table->string('nombre_completo', 255);
            $table->string('rfc', 13);
            $table->string('curp', 18)->nullable();
            $table->string('numero_escritura_poder', 100)->nullable();
            $table->date('fecha_escritura_poder')->nullable();
            $table->string('nombre_notario_poder', 255)->nullable();
            $table->string('numero_notario_poder', 50)->nullable();
            $table->string('entidad_federativa_notario_poder', 100)->nullable();
            $table->string('folio_registro_publico_poder', 50)->nullable();
            $table->date('fecha_inscripcion_poder')->nullable();
            $table->timestamps();
            
            $table->index('solicitante_id');
            $table->index('rfc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representantes_legales');
    }
};
