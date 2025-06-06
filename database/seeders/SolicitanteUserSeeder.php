<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SolicitanteUserSeeder extends Seeder
{
    public function run(): void
    {
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
            
            $this->command->info('Usuario solicitante creado con éxito.');
        } else {
            $this->command->info('El usuario solicitante ya existe.');
        }
    }
} 