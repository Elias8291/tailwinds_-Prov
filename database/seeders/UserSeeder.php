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
        $admin = User::firstOrCreate(['correo' => 'admin@example.com'], [
            'nombre' => 'Administrador',
            'rfc' => 'XAXX010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Create test users for each role
        $roles = ['revisor', 'solicitante', 'proveedor'];
        foreach ($roles as $role) {
            $user = User::firstOrCreate(['correo' => $role . '@example.com'], [
                'nombre' => ucfirst($role) . ' Test',
                'rfc' => strtoupper(substr($role, 0, 4)) . '010101000',
                'estado' => 'activo',
                'password' => Hash::make('password123'),
            ]);
            $user->assignRole($role);
        }

        // Crear un usuario solicitante específico para pruebas
        $solicitante = User::firstOrCreate(['correo' => 'juan.perez@empresa.com'], [
            'nombre' => 'Juan Pérez',
            'rfc' => 'PEJJ800101ABC',
            'estado' => 'activo',
            'password' => Hash::make('solicitante123'),
        ]);
        $solicitante->assignRole('solicitante');

        // Usuario solicitante con RFC simple para pruebas
        $testSolicitante = User::firstOrCreate(['rfc' => 'TEST010101000'], [
            'nombre' => 'Solicitante de Prueba',
            'correo' => 'test.solicitante@prueba.com',
            'estado' => 'activo', 
            'password' => Hash::make('123456'),
        ]);
        $testSolicitante->assignRole('solicitante');
    }
}
