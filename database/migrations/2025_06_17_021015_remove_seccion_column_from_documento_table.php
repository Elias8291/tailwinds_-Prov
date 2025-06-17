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
        Schema::table('documento', function (Blueprint $table) {
            $table->dropColumn('seccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento', function (Blueprint $table) {
            $table->enum('seccion', [
                'ninguna',
                'uno', 
                'dos', 
                'tres', 
                'cuatro', 
                'cinco', 
                'seis',
                '1',
                '2', 
                '3', 
                '4', 
                '5', 
                '6',
                'datos_generales',
                'domicilio',
                'documentos',
                'actividades',
                'accionistas',
                'finalizacion'
            ])->default('ninguna');
        });
    }
};
