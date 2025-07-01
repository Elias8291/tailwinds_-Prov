<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('actividad', function (Blueprint $table) {
            $table->string('codigo_scian', 10)->nullable()->after('nombre');
            $table->text('descripcion')->nullable()->after('codigo_scian');
            $table->string('fuente', 50)->default('MANUAL')->after('descripcion');
            $table->index(['codigo_scian']);
            $table->index(['nombre']);
        });
    }

    public function down()
    {
        Schema::table('actividad', function (Blueprint $table) {
            $table->dropIndex(['codigo_scian']);
            $table->dropIndex(['nombre']);
            $table->dropColumn(['codigo_scian', 'descripcion', 'fuente']);
        });
    }
}; 