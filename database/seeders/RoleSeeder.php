<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener o crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $revisorRole = Role::firstOrCreate(['name' => 'revisor', 'guard_name' => 'web']);
        $solicitanteRole = Role::firstOrCreate(['name' => 'solicitante', 'guard_name' => 'web']);
        $proveedorRole = Role::firstOrCreate(['name' => 'proveedor', 'guard_name' => 'web']);

        // Asignar permisos de dashboard
        $adminRole->givePermissionTo([
            'dashboard.admin',
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar'
        ]);
        $solicitanteRole->givePermissionTo('dashboard.solicitante');
    }
}