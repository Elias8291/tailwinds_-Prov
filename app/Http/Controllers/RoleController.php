<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\SystemLogService;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:roles.ver');
    }

    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array|nullable'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        if($request->has('permissions') && is_array($request->permissions)) {
            // Convertir IDs a nombres de permisos
            $permissionNames = $this->getPermissionNames($request->permissions);
            $role->syncPermissions($permissionNames);
        }

        // Log de creación de rol
        SystemLogService::roleCreated($role->id, $role->name);

        return redirect()->route('roles.index')
            ->with('success', '¡Rol creado exitosamente!');
    }

    public function show(Role $role)
    {
        $permissions = $role->permissions;
        return view('roles.show', compact('role', 'permissions'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array|nullable'
        ]);

        $role->update(['name' => $request->name]);

        if($request->has('permissions') && is_array($request->permissions)) {
            // Convertir IDs a nombres de permisos
            $permissionNames = $this->getPermissionNames($request->permissions);
            $role->syncPermissions($permissionNames);
        } else {
            // Si no se envían permisos, remover todos
            $role->syncPermissions([]);
        }

        // Log de actualización de rol
        SystemLogService::roleUpdated($role->id, $role->name);

        return redirect()->route('roles.index')
            ->with('success', '¡Rol actualizado exitosamente!');
    }

    public function destroy(Role $role)
    {
        // Proteger roles críticos del sistema
        $protectedRoles = ['Super Administrador', 'Administrador', 'Solicitante'];
        
        if(in_array($role->name, $protectedRoles)) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar un rol crítico del sistema');
        }

        // Log de eliminación de rol (antes de eliminar)
        SystemLogService::roleDeleted($role->id, $role->name);

        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', '¡Rol eliminado exitosamente!');
    }

    /**
     * Convierte array de IDs de permisos a nombres de permisos
     */
    private function getPermissionNames(array $permissionIds)
    {
        return Permission::whereIn('id', $permissionIds)
            ->pluck('name')
            ->toArray();
    }
} 