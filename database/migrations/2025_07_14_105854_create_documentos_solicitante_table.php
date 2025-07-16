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
        Schema::create('documentos_solicitante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites');
            $table->foreignId('documento_catalogo_id')->constrained('documentos_catalogo');
            $table->enum('estado_revision', ['pendiente', 'en_revision', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('comentarios_revision_actual')->nullable();
            $table->timestamp('fecha_cotejo_presencial')->nullable();
            $table->boolean('documento_cotejado')->default(false);
            $table->timestamps();
            
            $table->index('tramite_id');
            $table->index('estado_revision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_solicitante');
    }
};
