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
        Schema::table('solicitante', function (Blueprint $table) {
            // Solo agregar las columnas si no existen ya
            if (!Schema::hasColumn('solicitante', 'nombre_completo')) {
                $table->string('nombre_completo')->nullable()->after('rfc');
            }
            if (!Schema::hasColumn('solicitante', 'razon_social')) {
                $table->string('razon_social')->nullable()->after('nombre_completo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitante', function (Blueprint $table) {
            // Solo eliminar las columnas si existen
            if (Schema::hasColumn('solicitante', 'nombre_completo')) {
                $table->dropColumn('nombre_completo');
            }
            if (Schema::hasColumn('solicitante', 'razon_social')) {
                $table->dropColumn('razon_social');
            }
        });
    }
};
