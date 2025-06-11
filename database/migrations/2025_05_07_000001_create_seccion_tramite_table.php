<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seccion_tramite', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->integer('orden');
            $table->boolean('es_requerido')->default(true);
            $table->timestamps();
        });

        Schema::create('progreso_tramite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramite')->onDelete('cascade');
            $table->foreignId('seccion_id')->constrained('seccion_tramite')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'en_progreso', 'completado', 'revision', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_completado')->nullable();
            $table->timestamps();

            $table->unique(['tramite_id', 'seccion_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('progreso_tramite');
        Schema::dropIfExists('seccion_tramite');
    }
}; 