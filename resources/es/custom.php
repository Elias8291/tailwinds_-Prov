<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de idioma personalizadas
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar líneas de idioma personalizadas para tu aplicación.
    | Puedes usar estas líneas en cualquier lugar de tu aplicación.
    |
    */

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'roles' => 'roles',
        'rfc' => 'RFC',
        'phone' => 'teléfono',
        'address' => 'dirección',
        'first_name' => 'nombre',
        'last_name' => 'apellido',
        'company' => 'empresa',
        'position' => 'puesto',
        'description' => 'descripción',
        'status' => 'estado',
        'created_at' => 'fecha de creación',
        'updated_at' => 'fecha de actualización',
    ],

    'messages' => [
        'success' => [
            'created' => ':resource creado exitosamente.',
            'updated' => ':resource actualizado exitosamente.',
            'deleted' => ':resource eliminado exitosamente.',
            'restored' => ':resource restaurado exitosamente.',
        ],
        'error' => [
            'not_found' => ':resource no encontrado.',
            'unauthorized' => 'No tienes permisos para realizar esta acción.',
            'forbidden' => 'Acceso denegado.',
            'validation_failed' => 'Los datos proporcionados no son válidos.',
        ],
        'warning' => [
            'confirm_delete' => '¿Estás seguro de que deseas eliminar este :resource?',
            'permanent_action' => 'Esta acción no se puede deshacer.',
        ],
        'info' => [
            'empty_list' => 'No hay :resources disponibles.',
            'loading' => 'Cargando...',
            'processing' => 'Procesando...',
        ],
    ],

    'resources' => [
        'user' => 'usuario',
        'users' => 'usuarios',
        'role' => 'rol',
        'roles' => 'roles',
        'permission' => 'permiso',
        'permissions' => 'permisos',
        'document' => 'documento',
        'documents' => 'documentos',
        'file' => 'archivo',
        'files' => 'archivos',
    ],

]; 