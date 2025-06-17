<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestPermissions extends Command
{
    protected $signature = 'permissions:test';
    protected $description = 'Prueba la funcionalidad de permisos corregida';

    public function handle()
    {
        $this->info('🧪 Probando funcionalidad de permisos...');
        
        // Verificar permisos específicos
        $permision14 = Permission::find(14);
        if ($permision14) {
            $this->info("✅ Permiso ID 14: {$permision14->name}");
        } else {
            $this->warn("⚠️ Permiso ID 14 no encontrado");
        }

        // Probar conversión de IDs a nombres
        $testIds = [1, 14, 20];
        $names = Permission::whereIn('id', $testIds)->pluck('name')->toArray();
        
        $this->info("🔄 Conversión de IDs a nombres:");
        $this->table(['IDs de prueba', 'Nombres obtenidos'], [
            [implode(', ', $testIds), implode(', ', $names)]
        ]);

        // Verificar roles
        $roles = Role::with('permissions')->get();
        $this->info("📋 Roles existentes: " . $roles->count());
        
        foreach ($roles as $role) {
            $this->line("  • {$role->name}: {$role->permissions->count()} permisos");
        }

        $this->info("✅ Prueba completada");
        return 0;
    }
} 