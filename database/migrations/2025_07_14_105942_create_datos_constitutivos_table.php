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
        Schema::create('datos_constitutivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')->unique()->constrained('solicitantes');
            $table->string('numero_acta_constitutiva', 100)->nullable();
            $table->date('fecha_constitucion')->nullable();
            $table->string('nombre_notario', 255)->nullable();
            $table->string('numero_notaria', 50)->nullable();
            $table->string('entidad_federativa_notaria', 100)->nullable();
            $table->string('numero_registro_publico', 50)->nullable();
            $table->date('fecha_inscripcion_registro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_constitutivos');
    }
};
