<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seccion_revision', function (Blueprint $table) {
            $table->timestamp('fecha_revision')->nullable()->after('revisado_por');
        });
    }

    public function down(): void
    {
        Schema::table('seccion_revision', function (Blueprint $table) {
            $table->dropColumn('fecha_revision');
        });
    }
}; 