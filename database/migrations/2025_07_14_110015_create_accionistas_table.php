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
        Schema::create('accionistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')->constrained('solicitantes');
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->decimal('porcentaje_participacion', 5, 2)->nullable();
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
        Schema::dropIfExists('accionistas');
    }
};
