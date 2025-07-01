<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sector', function (Blueprint $table) {
            $table->string('codigo', 10)->nullable()->after('nombre');
            $table->text('descripcion')->nullable()->after('codigo');
            $table->index(['codigo']);
            $table->index(['nombre']);
        });
    }

    public function down()
    {
        Schema::table('sector', function (Blueprint $table) {
            $table->dropIndex(['codigo']);
            $table->dropIndex(['nombre']);
            $table->dropColumn(['codigo', 'descripcion']);
        });
    }
}; 