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
        Schema::create('oficios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramite')->onDelete('cascade');
            $table->string('numero_oficio')->unique();
            $table->string('tipo_oficio');
            $table->string('ruta_archivo');
            $table->string('hash_archivo')->nullable();
            $table->string('estado')->default('generado');
            $table->text('observaciones')->nullable();
            $table->foreignId('generado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficios');
    }
}; 