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
        Schema::table('direccion', function (Blueprint $table) {
            // Cambiar codigo_postal de int a varchar(5) para mantener el 0 inicial
            $table->string('codigo_postal', 5)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direccion', function (Blueprint $table) {
            // Revertir a int si es necesario
            $table->integer('codigo_postal')->change();
        });
    }
};
