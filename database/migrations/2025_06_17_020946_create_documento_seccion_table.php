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
        if (!Schema::hasTable('documento_seccion')) {
            Schema::create('documento_seccion', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('documento_id');
                $table->unsignedBigInteger('seccion_id');
                $table->timestamps();

                // Claves foráneas
                $table->foreign('documento_id')->references('id')->on('documento')->onDelete('cascade');
                $table->foreign('seccion_id')->references('id')->on('seccion_tramite')->onDelete('cascade');

                // Índice único para evitar duplicados
                $table->unique(['documento_id', 'seccion_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_seccion');
    }
};
