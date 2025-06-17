<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Ejecuta los seeds de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        // Limpiar permisos obsoletos primero
        $this->limpiarPermisosObsoletos();

        // Lista de permisos organizados y limpios
        $permisos = [
            // === DASHBOARD ===
            'dashboard.admin',
            'dashboard.solicitante', 
            'dashboard.revisor',

            // === GESTIÓN DE ROLES Y PERMISOS ===
            'roles.ver',
            'roles.crear',
            'roles.editar', 
            'roles.eliminar',
            'permisos.ver',
            'permisos.asignar',

            // === GESTIÓN DE USUARIOS ===
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'usuarios.asignar-roles',

            // === MIS TRÁMITES (SOLICITANTE) ===
            'tramites-solicitante.ver',
            'tramites-solicitante.crear',
            'tramites-solicitante.editar',
            'tramites-solicitante.inscripcion',
            'tramites-solicitante.renovacion',
            'tramites-solicitante.actualizacion',
            'tramites-solicitante.subir-documentos',
            'tramites-solicitante.finalizar',

            // === REVISIÓN DE TRÁMITES ===
            'revision-tramites.ver',
            'revision-tramites.revisar',
            'revision-tramites.aprobar',
            'revision-tramites.rechazar',
            'revision-tramites.comentarios',
            'revision-tramites.exportar',

            // === GESTIÓN DE DOCUMENTOS ===
            'documentos.ver',
            'documentos.crear',
            'documentos.editar',
            'documentos.eliminar',
            'documentos.descargar',
            'documentos.revisar',
            'documentos.validar',

            // === GESTIÓN DE PROVEEDORES ===
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.eliminar',
            'proveedores.activar',
            'proveedores.desactivar',
            'proveedores.exportar',
            'proveedores.historial',

            // === GESTIÓN DE CITAS ===
            'citas.ver',
            'citas.crear',
            'citas.editar',
            'citas.eliminar',
            'citas.agendar',
            'citas.cancelar',
            'citas.calendario',

            // === MI PERFIL ===
            'perfil.ver',
            'perfil.editar',
            'perfil.cambiar-password',

            // === CONFIGURACIÓN ===
            'configuracion.ver',
            'configuracion.sistema',
            'configuracion.notificaciones',
            'configuracion.backup',
            'configuracion.logs',

            // === PERMISOS ESPECIALES ===
            'super-admin',
            'admin-sistema',
            'revisor-documentos',
            'gestor-proveedores',
        ];

        // Crear cada permiso en la base de datos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
        }

        // Mostrar información al ejecutar el seeder
        $this->command->info('✅ Se han creado ' . count($permisos) . ' permisos organizados exitosamente.');
    }

    /**
     * Eliminar permisos obsoletos que ya no se utilizan
     */
    private function limpiarPermisosObsoletos()
    {
        $permisosObsoletos = [
            // Permisos obsoletos del padrón de proveedores
            'padron-proveedores.ver',
            'padron-proveedores.registrar',
            'padron-proveedores.editar',
            'padron-proveedores.eliminar',
            'padron-proveedores.validar',

            // Permisos duplicados obsoletos
            'solicitantes.registrar',
            'solicitantes.ver',
            'solicitantes.editar',
            'solicitantes.eliminar',

            // Permisos antiguos de documentos
            'documentos.cargar',
            'documentos.index',
            'documentos.create',
            'documentos.edit',
            'documentos.destroy',

            // Permisos antiguos de usuarios  
            'usuarios.index',
            'usuarios.create',
            'usuarios.edit',
            'usuarios.destroy',
        ];

        foreach ($permisosObsoletos as $permiso) {
            $permission = Permission::where('name', $permiso)->where('guard_name', 'web')->first();
            if ($permission) {
                $permission->delete();
                $this->command->warn("🗑️  Eliminado permiso obsoleto: {$permiso}");
            }
        }
    }
}