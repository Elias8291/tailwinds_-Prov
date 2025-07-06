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
        Schema::table('documento_solicitante', function (Blueprint $table) {
            $table->boolean('documento_cotejado')->default(false)->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_solicitante', function (Blueprint $table) {
            $table->dropColumn('documento_cotejado');
        });
    }
};
