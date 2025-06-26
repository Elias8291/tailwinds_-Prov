<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o crear el rol de Solicitante
        $roleSolicitante = Role::where('name', 'Solicitante')->first();
        
        if (!$roleSolicitante) {
            $this->command->error('El rol Solicitante no existe. Ejecute primero RoleSeeder.');
            return;
        }

        // Buscar el primer usuario disponible
        $usuario = User::first();
        
        if (!$usuario) {
            // Crear un usuario de prueba si no existe ninguno
            $usuario = User::create([
                'nombre' => 'Usuario de Prueba',
                'correo' => 'test@example.com',
                'rfc' => 'XAXX010101000',
                'password' => bcrypt('password'),
                'estado' => 'activo',
                'fecha_verificacion_correo' => now(),
            ]);
        }

        // Asignar el rol si no lo tiene
        if (!$usuario->hasRole('Solicitante')) {
            $usuario->assignRole('Solicitante');
            $this->command->info("Rol 'Solicitante' asignado al usuario: {$usuario->nombre}");
        } else {
            $this->command->info("El usuario '{$usuario->nombre}' ya tiene el rol 'Solicitante'");
        }

        // Verificar permisos
        if ($usuario->can('mi-estado-proveedor.ver')) {
            $this->command->info("✅ El usuario puede acceder a 'mi-estado-proveedor.ver'");
        } else {
            $this->command->warn("⚠️ El usuario NO puede acceder a 'mi-estado-proveedor.ver'");
        }
    }
} 