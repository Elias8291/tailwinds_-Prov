<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemLogService
{
    /**
     * Registrar una acción del sistema
     */
    public static function log(string $level, string $message, string $channel = 'system', array $context = [], ?Request $request = null)
    {
        $request = $request ?? request();
        
        Log::create([
            'level' => $level,
            'message' => $message,
            'channel' => $channel,
            'context' => !empty($context) ? json_encode($context) : null,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);
    }

    /**
     * Log de información
     */
    public static function info(string $message, string $channel = 'system', array $context = [])
    {
        self::log('info', $message, $channel, $context);
    }

    /**
     * Log de advertencia
     */
    public static function warning(string $message, string $channel = 'system', array $context = [])
    {
        self::log('warning', $message, $channel, $context);
    }

    /**
     * Log de error
     */
    public static function error(string $message, string $channel = 'system', array $context = [])
    {
        self::log('error', $message, $channel, $context);
    }

    /**
     * Log de debug
     */
    public static function debug(string $message, string $channel = 'system', array $context = [])
    {
        self::log('debug', $message, $channel, $context);
    }

    // === LOGS ESPECÍFICOS PARA ACCIONES DEL SISTEMA ===

    /**
     * Logs de autenticación
     */
    public static function userLogin(string $email)
    {
        self::info("Usuario ha iniciado sesión: {$email}", 'auth', ['email' => $email]);
    }

    public static function userLogout(string $email)
    {
        self::info("Usuario ha cerrado sesión: {$email}", 'auth', ['email' => $email]);
    }

    public static function loginFailed(string $email)
    {
        self::warning("Intento de login fallido: {$email}", 'auth', ['email' => $email]);
    }

    /**
     * Logs de usuarios
     */
    public static function userCreated(int $userId, string $name, string $email)
    {
        self::info("Usuario creado: {$name} ({$email})", 'usuarios', [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email
        ]);
    }

    public static function userUpdated(int $userId, string $name, string $email)
    {
        self::info("Usuario actualizado: {$name} ({$email})", 'usuarios', [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email
        ]);
    }

    public static function userDeleted(int $userId, string $name, string $email)
    {
        self::warning("Usuario eliminado: {$name} ({$email})", 'usuarios', [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email
        ]);
    }

    public static function userRoleAssigned(int $userId, string $userName, string $roleName)
    {
        self::info("Rol asignado a usuario: {$userName} -> {$roleName}", 'permisos', [
            'user_id' => $userId,
            'user_name' => $userName,
            'role_name' => $roleName
        ]);
    }

    /**
     * Logs de roles y permisos
     */
    public static function roleCreated(int $roleId, string $roleName)
    {
        self::info("Rol creado: {$roleName}", 'permisos', [
            'role_id' => $roleId,
            'role_name' => $roleName
        ]);
    }

    public static function roleUpdated(int $roleId, string $roleName)
    {
        self::info("Rol actualizado: {$roleName}", 'permisos', [
            'role_id' => $roleId,
            'role_name' => $roleName
        ]);
    }

    public static function roleDeleted(int $roleId, string $roleName)
    {
        self::warning("Rol eliminado: {$roleName}", 'permisos', [
            'role_id' => $roleId,
            'role_name' => $roleName
        ]);
    }

    /**
     * Logs de trámites
     */
    public static function tramiteCreated(int $tramiteId, string $tipo, string $solicitante)
    {
        self::info("Trámite creado: {$tipo} para {$solicitante}", 'tramites', [
            'tramite_id' => $tramiteId,
            'tipo_tramite' => $tipo,
            'solicitante' => $solicitante
        ]);
    }

    public static function tramiteUpdated(int $tramiteId, string $tipo, string $solicitante)
    {
        self::info("Trámite actualizado: {$tipo} para {$solicitante}", 'tramites', [
            'tramite_id' => $tramiteId,
            'tipo_tramite' => $tipo,
            'solicitante' => $solicitante
        ]);
    }

    public static function tramiteFinalized(int $tramiteId, string $tipo, string $solicitante)
    {
        self::info("Trámite finalizado: {$tipo} para {$solicitante}", 'tramites', [
            'tramite_id' => $tramiteId,
            'tipo_tramite' => $tipo,
            'solicitante' => $solicitante
        ]);
    }

    public static function tramiteApproved(int $tramiteId, string $tipo, string $solicitante)
    {
        self::info("Trámite aprobado: {$tipo} para {$solicitante}", 'revision', [
            'tramite_id' => $tramiteId,
            'tipo_tramite' => $tipo,
            'solicitante' => $solicitante
        ]);
    }

    public static function tramiteRejected(int $tramiteId, string $tipo, string $solicitante, string $reason = '')
    {
        self::warning("Trámite rechazado: {$tipo} para {$solicitante}", 'revision', [
            'tramite_id' => $tramiteId,
            'tipo_tramite' => $tipo,
            'solicitante' => $solicitante,
            'reason' => $reason
        ]);
    }

    /**
     * Logs de documentos
     */
    public static function documentUploaded(string $fileName, int $tramiteId, string $solicitante)
    {
        self::info("Documento subido: {$fileName} para trámite {$tramiteId}", 'documentos', [
            'file_name' => $fileName,
            'tramite_id' => $tramiteId,
            'solicitante' => $solicitante
        ]);
    }

    public static function documentDeleted(string $fileName, int $tramiteId, string $solicitante)
    {
        self::warning("Documento eliminado: {$fileName} del trámite {$tramiteId}", 'documentos', [
            'file_name' => $fileName,
            'tramite_id' => $tramiteId,
            'solicitante' => $solicitante
        ]);
    }

    /**
     * Logs de proveedores
     */
    public static function proveedorCreated(int $proveedorId, string $nombre)
    {
        self::info("Proveedor creado: {$nombre}", 'proveedores', [
            'proveedor_id' => $proveedorId,
            'nombre' => $nombre
        ]);
    }

    public static function proveedorUpdated(int $proveedorId, string $nombre)
    {
        self::info("Proveedor actualizado: {$nombre}", 'proveedores', [
            'proveedor_id' => $proveedorId,
            'nombre' => $nombre
        ]);
    }

    public static function proveedorDeleted(int $proveedorId, string $nombre)
    {
        self::warning("Proveedor eliminado: {$nombre}", 'proveedores', [
            'proveedor_id' => $proveedorId,
            'nombre' => $nombre
        ]);
    }

    /**
     * Logs de citas
     */
    public static function citaCreated(int $citaId, string $fecha, string $solicitante)
    {
        self::info("Cita creada: {$fecha} para {$solicitante}", 'citas', [
            'cita_id' => $citaId,
            'fecha' => $fecha,
            'solicitante' => $solicitante
        ]);
    }

    public static function citaCanceled(int $citaId, string $fecha, string $solicitante)
    {
        self::warning("Cita cancelada: {$fecha} para {$solicitante}", 'citas', [
            'cita_id' => $citaId,
            'fecha' => $fecha,
            'solicitante' => $solicitante
        ]);
    }

    /**
     * Logs de seguridad
     */
    public static function accessDenied(string $permission, string $url)
    {
        self::warning("Acceso denegado por permisos insuficientes", 'security', [
            'required_permission' => $permission,
            'attempted_url' => $url
        ]);
    }

    public static function suspiciousActivity(string $activity, array $details = [])
    {
        self::error("Actividad sospechosa detectada: {$activity}", 'security', $details);
    }
} 