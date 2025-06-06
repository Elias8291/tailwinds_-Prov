<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'nombre' => 'Administrador',
            'correo' => 'admin@example.com',
            'rfc' => 'XAXX010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Create test users for each role
        $roles = ['revisor', 'solicitante', 'proveedor'];
        foreach ($roles as $role) {
            $user = User::factory()->create([
                'nombre' => ucfirst($role) . ' Test',
                'correo' => $role . '@example.com',
                'estado' => 'activo',
                'password' => Hash::make('password123'),
            ]);
            $user->assignRole($role);
        }

        // Crear un usuario solicitante adicional si no existe
        if (!User::where('correo', 'juan.perez@empresa.com')->exists()) {
            $solicitante = User::factory()->create([
                'nombre' => 'Juan Pérez',
                'correo' => 'juan.perez@empresa.com',
                'rfc' => 'PEJJ800101ABC',
                'estado' => 'activo',
                'password' => Hash::make('solicitante123'),
            ]);
            $solicitante->assignRole('solicitante');
        }
    }
}
