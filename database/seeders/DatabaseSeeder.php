<?php

namespace Database\Seeders;

use App\Models\DetalleTramite;
use App\Models\Solicitante;
use App\Models\TipoAsentamiento;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando proceso de seeding...');

        $seeders = [
            'Permisos' => PermissionSeeder::class,
            'Roles' => RoleSeeder::class,
            'Usuarios' => UserSeeder::class,
            'Países' => PaisSeeder::class,
            'Estados' => EstadosTableSeeder::class,
            'Municipios' => MunicipioSeeder::class,
            'Localidades' => LocalidadSeeder::class,
            'Tipos de Asentamiento' => TiposAsentamientoSeeder::class,
            'Asentamientos' => AsentamientosSeeder::class,
            'Documentos' => DocumentoSeeder::class,
            'Sectores' => SectoresSeeder::class,
            'Actividades' => ActividadesSeeder::class,
            'Días Inhábiles' => DiasInhabilesSeeder::class,
            'Solicitantes' => SolicitanteSeeder::class,
            'Trámites' => TramiteSeeder::class,
            'Detalles de Trámite' => DetalleTramiteSeeder::class,
            'Proveedores' => ProveedorSeeder::class,
            'Secciones de Trámite' => SeccionTramiteSeeder::class,
            'Permisos de Usuario' => UserPermissionSeeder::class,
        ];

        $total = count($seeders);
        $current = 0;

        foreach ($seeders as $name => $seeder) {
            $current++;
            $this->command->info(sprintf(
                '(%d/%d) Ejecutando %s...',
                $current,
                $total,
                $name
            ));
            
            $this->call($seeder);
        }

        $this->command->info('✅ Proceso de seeding completado exitosamente');
    }
}
