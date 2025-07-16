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
        Schema::create('revisiones_seccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seccion_tramite_id')->constrained('secciones_tramite');
            $table->foreignId('revisor_id')->constrained('users');
            $table->text('comentarios')->nullable();
            $table->enum('estado_revision', ['aprobado', 'con_observaciones']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisiones_seccion');
    }
};
