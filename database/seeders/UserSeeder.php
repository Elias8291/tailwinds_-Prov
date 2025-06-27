<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Administrador
        $superAdmin = User::firstOrCreate(['correo' => '20161273@itoaxaca.edu.mx'], [
            'nombre' => 'Super Administrador',
            'rfc' => 'RAJE020226G97',
            'estado' => 'activo',
            'password' => Hash::make('gSSKAtlVP'),
        ]);
        $superAdmin->assignRole('Super Administrador');

        // Administrador
        $admin = User::firstOrCreate(['correo' => 'admin@example.com'], [
            'nombre' => 'Administrador',
            'rfc' => 'XAXX010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('Administrador');

        $this->command->info('Usuarios creados correctamente');
    }
}
