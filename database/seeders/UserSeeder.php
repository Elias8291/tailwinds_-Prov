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
        $superAdmin = User::firstOrCreate(['correo' => '20161302@itoaxaca.edu.mx'], [
            'nombre' => 'Elias Abisai Ramos Jacinto',
            'rfc' => 'RAJE020226G97',
            'estado' => 'activo',
            'password' => Hash::make('gSSKAtlVP'),
        ]);
        $superAdmin->assignRole('Super Administrador');

        // Administrador
        $admin = User::firstOrCreate(['correo' => '20161273@itoaxaca.edu.mx'], [
            'nombre' => 'Jacqueline Patricia Miguel Pensamiento Dominguez',
            'rfc' => 'MIDJ020222G49',
            'estado' => 'activo',
            'password' => Hash::make('vHiTUCYQ'),
        ]);
        $admin->assignRole('Administrador');

        $this->command->info('Usuarios creados correctamente');
    }
}
