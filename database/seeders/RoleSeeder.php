<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Ejecuta los seeds de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        // === SUPER ADMINISTRADOR ===
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Administrador',
            'guard_name' => 'web'
        ]);
        
        // El super admin tiene TODOS los permisos
        $superAdmin->givePermissionTo(Permission::all());

        // === ADMINISTRADOR ===
        $admin = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'web'
        ]);
        
        $permisosAdmin = [
            // Dashboard
            'dashboard.admin',
            
            // Roles y permisos
            'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar',
            'permisos.ver', 'permisos.asignar',
            
            // Usuarios
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar', 'usuarios.asignar-roles',
            
            // Revisión de trámites
            'revision-tramites.ver', 'revision-tramites.revisar', 'revision-tramites.aprobar', 
            'revision-tramites.rechazar', 'revision-tramites.comentarios', 'revision-tramites.exportar',
            
            // Documentos
            'documentos.ver', 'documentos.crear', 'documentos.editar', 'documentos.eliminar',
            'documentos.descargar', 'documentos.revisar', 'documentos.validar',
            
            // Proveedores
            'proveedores.ver', 'proveedores.crear', 'proveedores.editar', 'proveedores.eliminar',
            'proveedores.activar', 'proveedores.desactivar', 'proveedores.exportar', 'proveedores.historial',
            
            // Citas
            'citas.ver', 'citas.crear', 'citas.editar', 'citas.eliminar',
            'citas.agendar', 'citas.cancelar', 'citas.calendario',
            
            // Configuración
            'configuracion.ver', 'configuracion.sistema', 'configuracion.notificaciones',
            'configuracion.backup', 'configuracion.logs',
            
            // Perfil
            'perfil.ver', 'perfil.editar', 'perfil.cambiar-password',
        ];

        $this->asignarPermisosSeguro($admin, $permisosAdmin);

        // === REVISOR DE TRÁMITES ===
        $revisor = Role::firstOrCreate([
            'name' => 'Revisor de Trámites',
            'guard_name' => 'web'
        ]);
        
        $permisosRevisor = [
            'dashboard.revisor',
            'revision-tramites.ver', 'revision-tramites.revisar', 'revision-tramites.aprobar',
            'revision-tramites.rechazar', 'revision-tramites.comentarios',
            'documentos.ver', 'documentos.descargar', 'documentos.revisar', 'documentos.validar',
            'proveedores.ver', 'proveedores.historial',
            'perfil.ver', 'perfil.editar', 'perfil.cambiar-password',
        ];

        $this->asignarPermisosSeguro($revisor, $permisosRevisor);

        // === GESTOR DE PROVEEDORES ===
        $gestorProveedores = Role::firstOrCreate([
            'name' => 'Gestor de Proveedores',
            'guard_name' => 'web'
        ]);
        
        $permisosGestor = [
            'dashboard.admin',
            'proveedores.ver', 'proveedores.crear', 'proveedores.editar',
            'proveedores.activar', 'proveedores.desactivar', 'proveedores.exportar', 'proveedores.historial',
            'documentos.ver', 'documentos.descargar', 'documentos.revisar',
            'citas.ver', 'citas.crear', 'citas.editar', 'citas.eliminar',
            'citas.agendar', 'citas.cancelar', 'citas.calendario',
            'perfil.ver', 'perfil.editar', 'perfil.cambiar-password',
        ];

        $this->asignarPermisosSeguro($gestorProveedores, $permisosGestor);

        // === SOLICITANTE ===
        $solicitante = Role::firstOrCreate([
            'name' => 'Solicitante',
            'guard_name' => 'web'
        ]);
        
        $permisosSolicitante = [
            'dashboard.solicitante',
            'tramites-solicitante.ver', 'tramites-solicitante.crear', 'tramites-solicitante.editar',
            'tramites-solicitante.inscripcion', 'tramites-solicitante.renovacion', 'tramites-solicitante.actualizacion',
            'tramites-solicitante.subir-documentos', 'tramites-solicitante.finalizar',
            'perfil.ver', 'perfil.editar', 'perfil.cambiar-password',
        ];

        $this->asignarPermisosSeguro($solicitante, $permisosSolicitante);

        // === OPERADOR ===
        $operador = Role::firstOrCreate([
            'name' => 'Operador',
            'guard_name' => 'web'
        ]);
        
        $permisosOperador = [
            'dashboard.admin',
            'documentos.ver', 'documentos.crear', 'documentos.editar', 'documentos.descargar',
            'citas.ver', 'citas.crear', 'citas.editar', 'citas.agendar', 'citas.cancelar', 'citas.calendario',
            'proveedores.ver', 'proveedores.historial',
            'perfil.ver', 'perfil.editar', 'perfil.cambiar-password',
        ];

        $this->asignarPermisosSeguro($operador, $permisosOperador);

        $this->command->info('✅ Se han actualizado los roles con permisos organizados y limpios.');
    }

    /**
     * Asigna permisos de manera segura, ignorando los que no existen
     */
    private function asignarPermisosSeguro($role, $permisos)
    {
        $permisosExistentes = [];
        
        foreach ($permisos as $permiso) {
            if (Permission::where('name', $permiso)->where('guard_name', 'web')->exists()) {
                $permisosExistentes[] = $permiso;
            } else {
                $this->command->warn("⚠️  Permiso '{$permiso}' no existe, se omite para el rol '{$role->name}'");
            }
        }

        if (!empty($permisosExistentes)) {
            $role->syncPermissions($permisosExistentes);
        }
    }
}