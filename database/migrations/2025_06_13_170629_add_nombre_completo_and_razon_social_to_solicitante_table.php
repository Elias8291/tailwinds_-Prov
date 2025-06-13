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
            $table->string('nombre_completo')->nullable()->after('rfc');
            $table->string('razon_social')->nullable()->after('nombre_completo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitante', function (Blueprint $table) {
            $table->dropColumn(['nombre_completo', 'razon_social']);
        });
    }
};
