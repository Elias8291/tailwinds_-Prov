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
            'Catálogo de Países' => CatalogoPaisesSeeder::class,
            'Catálogo de Estados' => CatalogoEstadosSeeder::class,
            'Catálogo de Municipios' => CatalogoMunicipiosSeeder::class,
            'Catálogo de Localidades' => CatalogoLocalidadesSeeder::class,
            'Catálogo de Tipos de Asentamiento' => CatalogoTiposAsentamientoSeeder::class,
            'Catálogo de Asentamientos' => CatalogoAsentamientosSeeder::class,
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
