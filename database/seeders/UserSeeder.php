<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // === SUPER ADMINISTRADOR ===
        $superAdmin = User::firstOrCreate(['correo' => '20161273@itoaxaca.edu.mx'], [
            'nombre' => 'Super Administrador',
            'rfc' => 'MIDJ020222G49',
            'estado' => 'activo',
            'password' => Hash::make('gSSKAtlVP'),
        ]);
        $superAdmin->assignRole('Super Administrador');

        // === ADMINISTRADOR ===
        $admin = User::firstOrCreate(['correo' => 'admin@example.com'], [
            'nombre' => 'Administrador',
            'rfc' => 'XAXX010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('Administrador');

        // === REVISOR DE TRÁMITES ===
        $revisor = User::firstOrCreate(['correo' => 'revisor@example.com'], [
            'nombre' => 'Revisor Test',
            'rfc' => 'REVI010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $revisor->assignRole('Revisor de Trámites');

        // === GESTOR DE PROVEEDORES ===
        $gestorProveedores = User::firstOrCreate(['correo' => 'gestor@example.com'], [
            'nombre' => 'Gestor de Proveedores',
            'rfc' => 'GEST010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $gestorProveedores->assignRole('Gestor de Proveedores');

        // === OPERADOR ===
        $operador = User::firstOrCreate(['correo' => 'operador@example.com'], [
            'nombre' => 'Operador Test',
            'rfc' => 'OPER010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $operador->assignRole('Operador');

        // === SOLICITANTES ===
        $solicitante1 = User::firstOrCreate(['correo' => 'solicitante@example.com'], [
            'nombre' => 'Solicitante Test',
            'rfc' => 'SOLI010101000',
            'estado' => 'activo',
            'password' => Hash::make('password123'),
        ]);
        $solicitante1->assignRole('Solicitante');

        // Solicitante específico para pruebas
        $solicitante2 = User::firstOrCreate(['correo' => 'juan.perez@empresa.com'], [
            'nombre' => 'Juan Pérez',
            'rfc' => 'PEJJ800101ABC',
            'estado' => 'activo',
            'password' => Hash::make('solicitante123'),
        ]);
        $solicitante2->assignRole('Solicitante');

        // Usuario solicitante con RFC simple para pruebas
        $testSolicitante = User::firstOrCreate(['rfc' => 'TEST010101000'], [
            'nombre' => 'Solicitante de Prueba',
            'correo' => 'test.solicitante@prueba.com',
            'estado' => 'activo', 
            'password' => Hash::make('123456'),
        ]);
        $testSolicitante->assignRole('Solicitante');

        // Usuario LUIS FELIPE que ya existe
        $luisFelipe = User::firstOrCreate(['correo' => 'lufemer@hotmail.com'], [
            'nombre' => 'LUIS FELIPE MENDOZA RUIZ',
            'rfc' => 'MERL123456ABC',
            'estado' => 'activo',
            'password' => Hash::make('123456'),
        ]);
        $luisFelipe->assignRole('Solicitante');

        $this->command->info('✅ Usuarios creados y roles asignados correctamente.');
        $this->command->info('🔑 Super Admin: 20161273@itoaxaca.edu.mx / gSSKAtlVP');
    }
}
