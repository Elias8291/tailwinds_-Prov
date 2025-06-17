<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AsignarPermisosUsuarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-permissions 
                            {--admin : Asignar rol de administrador}
                            {--super : Asignar rol de super administrador}
                            {--email= : Email del usuario específico}
                            {--role= : Nombre del rol a asignar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna roles y permisos a usuarios existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Sistema de Asignación de Roles y Permisos');
        $this->info('================================================');

        // Verificar si se especificó un email específico
        if ($this->option('email')) {
            $this->asignarPermisoUsuarioEspecifico();
        } else if ($this->option('admin')) {
            $this->asignarAdministradores();
        } else if ($this->option('super')) {
            $this->asignarSuperAdministrador();
        } else {
            $this->mostrarMenuInteractivo();
        }

        return 0;
    }

    /**
     * Mostrar menú interactivo para asignar roles
     */
    private function mostrarMenuInteractivo()
    {
        $usuarios = User::all();
        
        if ($usuarios->isEmpty()) {
            $this->error('❌ No hay usuarios en el sistema.');
            return;
        }

        $this->table(
            ['ID', 'Nombre', 'Email', 'Roles Actuales'],
            $usuarios->map(function ($user) {
                return [
                    $user->id,
                    $user->name ?? 'N/A',
                    $user->correo,
                    $user->roles->pluck('name')->join(', ') ?: 'Sin roles'
                ];
            })
        );

        $usuarioId = $this->ask('💬 Ingresa el ID del usuario');
        $usuario = User::find($usuarioId);

        if (!$usuario) {
            $this->error('❌ Usuario no encontrado.');
            return;
        }

        $rolesDisponibles = Role::all()->pluck('name')->toArray();
        
        $this->info("\n📋 Roles disponibles:");
        foreach ($rolesDisponibles as $index => $rol) {
            $this->line(($index + 1) . ". " . $rol);
        }

        $rolIndex = $this->ask('💬 Selecciona el número del rol') - 1;
        
        if (!isset($rolesDisponibles[$rolIndex])) {
            $this->error('❌ Rol no válido.');
            return;
        }

        $rolSeleccionado = $rolesDisponibles[$rolIndex];
        
        // Limpiar roles anteriores y asignar el nuevo
        $usuario->syncRoles([$rolSeleccionado]);
        
        $this->info("✅ Rol '{$rolSeleccionado}' asignado exitosamente a {$usuario->correo}");
    }

    /**
     * Asignar rol de super administrador
     */
    private function asignarSuperAdministrador()
    {
        $email = $this->option('email') ?: $this->ask('💬 Email del usuario para Super Administrador');
        
        $usuario = User::where('correo', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario con email '{$email}' no encontrado.");
            return;
        }

        $usuario->syncRoles(['Super Administrador']);
        $this->info("✅ {$usuario->correo} ahora es Super Administrador");
        $this->mostrarPermisosUsuario($usuario);
    }

    /**
     * Asignar rol de administrador a usuarios específicos
     */
    private function asignarAdministradores()
    {
        $usuariosAdmin = [
            'admin@sepech.gob.mx',
            'director@sepech.gob.mx',
            'coordinador@sepech.gob.mx'
        ];

        foreach ($usuariosAdmin as $email) {
            $usuario = User::where('correo', $email)->first();
            
            if ($usuario) {
                $usuario->syncRoles(['Administrador']);
                $this->info("✅ {$email} → Administrador");
            } else {
                $this->warn("⚠️  Usuario {$email} no encontrado");
            }
        }
    }

    /**
     * Asignar permiso a usuario específico
     */
    private function asignarPermisoUsuarioEspecifico()
    {
        $email = $this->option('email');
        $rol = $this->option('role');
        
        $usuario = User::where('correo', $email)->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario con email '{$email}' no encontrado.");
            return;
        }

        if ($rol) {
            $roleExists = Role::where('name', $rol)->exists();
            
            if (!$roleExists) {
                $this->error("❌ Rol '{$rol}' no existe.");
                return;
            }

            $usuario->syncRoles([$rol]);
            $this->info("✅ Rol '{$rol}' asignado a {$usuario->correo}");
        }

        $this->mostrarPermisosUsuario($usuario);
    }

    /**
     * Mostrar permisos de un usuario
     */
    private function mostrarPermisosUsuario($usuario)
    {
        $this->info("\n👤 Información de permisos para: {$usuario->correo}");
        $this->info("📋 Roles: " . $usuario->roles->pluck('name')->join(', '));
        
        $permisos = $usuario->getAllPermissions()->pluck('name')->sort();
        
        if ($permisos->isNotEmpty()) {
            $this->info("🔑 Permisos ({$permisos->count()}):");
            
            $permisosAgrupados = $permisos->groupBy(function ($permiso) {
                return explode('.', $permiso)[0];
            });

            foreach ($permisosAgrupados as $modulo => $permisosMod) {
                $this->line("  📁 {$modulo}: " . $permisosMod->map(function($p) { 
                    return explode('.', $p)[1] ?? $p; 
                })->join(', '));
            }
        } else {
            $this->warn("⚠️  Sin permisos asignados");
        }
    }
} 