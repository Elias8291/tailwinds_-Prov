<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En MySQL necesitamos modificar el enum directamente
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tramite MODIFY COLUMN estado ENUM('Pendiente', 'En Revision', 'Aprobado', 'Rechazado', 'Por Cotejar', 'Cancelado') DEFAULT 'Pendiente'");
        }
        // Para PostgreSQL (si lo usas en el futuro)
        else if (DB::connection()->getDriverName() === 'pgsql') {
            // Primero crear el nuevo tipo
            DB::statement("ALTER TYPE tramite_estado_enum ADD VALUE 'Cancelado'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En MySQL volvemos al enum original
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tramite MODIFY COLUMN estado ENUM('Pendiente', 'En Revision', 'Aprobado', 'Rechazado', 'Por Cotejar') DEFAULT 'Pendiente'");
        }
        // Para PostgreSQL no podemos eliminar valores de enum, así que no hacemos nada en down()
    }
}; 