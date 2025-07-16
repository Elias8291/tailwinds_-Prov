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
        Schema::create('actividades_catalogo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained('sectores_catalogo');
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->boolean('es_personalizada')->default(false)->comment('True si fue agregada por un usuario y requiere clasificación');
            $table->timestamps();
            
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades_catalogo');
    }
};
