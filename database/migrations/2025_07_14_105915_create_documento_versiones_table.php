<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documento_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_solicitante_id')->constrained('documentos_solicitante');
            $table->string('path_archivo', 255);
            $table->string('hash_archivo', 255)->nullable();
            $table->timestamp('fecha_subida')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('comentarios_subida')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_versiones');
    }
};
