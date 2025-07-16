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
        Schema::create('solicitantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users');
            $table->string('rfc', 13)->unique()->comment('Indexado por ser único y para búsquedas');
            $table->string('nombre_razon_social', 255);
            $table->enum('persona_tipo', ['fisica', 'moral']);
            $table->string('curp', 18)->nullable()->comment('Requerido para personas físicas');
            $table->text('giro')->nullable()->comment('Descripción comercial breve. No usar para análisis.');
            $table->string('pagina_web', 255)->nullable();
            $table->string('telefono_celular', 20)->nullable();
            $table->string('telefono_oficina', 20)->nullable();
            $table->timestamps();
            
            $table->index('nombre_razon_social');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitantes');
    }
};
