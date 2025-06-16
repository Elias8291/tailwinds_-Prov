<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seccion_revision', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramite')->onDelete('cascade');
            $table->foreignId('seccion_id')->constrained('seccion_tramite')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('comentario')->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['tramite_id', 'seccion_id'], 'tramite_seccion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seccion_revision');
    }
}; 