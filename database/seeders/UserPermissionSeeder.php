<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrador']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);

        // Crear Super Administrador
        $superAdmin = User::firstOrCreate(
            ['correo' => '20161302@itoaxaca.edu.mx'],
            [
                'nombre' => 'Super Administrador',
                'rfc' => 'RAJE020226G97',
                'password' => Hash::make('Abisai1456'),
                'fecha_verificacion_correo' => now(),
                'estado' => 'activo',
            ]
        );

        // Crear Administrador
        $admin = User::firstOrCreate(
            ['correo' => 'admin@sistema.com'],
            [
                'nombre' => 'Administrador',
                'rfc' => 'ADMIN123456789',
                'password' => Hash::make('admin123'),
                'fecha_verificacion_correo' => now(),
                'estado' => 'activo',
            ]
        );

        // Asignar roles
        $superAdmin->assignRole($superAdminRole);
        $admin->assignRole($adminRole);

        $this->command->info('Usuarios y permisos creados exitosamente');
    }
}
