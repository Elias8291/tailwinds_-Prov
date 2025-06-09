# Configuración de Seguridad de Sesiones

Este documento describe las medidas de seguridad implementadas para prevenir la navegación no autorizada entre páginas autenticadas y no autenticadas.

## 🔒 Características Implementadas

### 1. Control de Acceso por Middleware

#### Middleware `guest`
- **Propósito**: Previene que usuarios autenticados accedan a páginas de login/registro
- **Rutas protegidas**: 
  - `/` (welcome)
  - `/iniciar-sesion` (login)
  - `/registro` (register)
  - `/recuperar-password` (password reset)

#### Middleware `auth`
- **Propósito**: Requiere autenticación para acceder a páginas del dashboard
- **Rutas protegidas**: Todas las rutas de dashboard, gestión de usuarios, roles, etc.

### 2. Expiración Automática de Sesiones

#### Middleware `CheckSessionExpiry`
- **Ubicación**: `app/Http/Middleware/CheckSessionExpiry.php`
- **Funcionalidad**:
  - Verifica la última actividad del usuario
  - Cierra automáticamente sesiones inactivas después de 30 minutos (configurable)
  - Redirige al login con mensaje informativo
  - Actualiza la marca de tiempo de última actividad en cada request

#### Configuración
```bash
# Configurar tiempo de vida de sesión (en minutos)
php artisan session:configure-security --lifetime=30
```

### 3. Headers Anti-Cache

#### Middleware `cache.headers:no-cache`
- **Propósito**: Previene que el navegador cachee páginas sensibles
- **Headers enviados**:
  - `Cache-Control: no-cache, no-store, must-revalidate`
  - `Pragma: no-cache`
  - `Expires: 0`

### 4. JavaScript Anti-Navegación

#### Protección del Historial del Navegador
- **Ubicación**: 
  - `resources/views/layouts/app.blade.php`
  - `resources/views/layouts/auth.blade.php`
- **Funcionalidades**:
  - Bloquea botones atrás/adelante del navegador
  - Previene teclas de navegación (Alt+←, Alt+→)
  - Bloquea tecla Backspace fuera de campos de texto

```javascript
// Prevenir navegación con botones del navegador
history.pushState(null, null, location.href);
window.addEventListener('popstate', function(event) {
    history.pushState(null, null, location.href);
});
```

## 🚀 Cómo Funciona

### Flujo de Usuario No Autenticado
1. **Acceso inicial**: Usuario puede ver welcome, login, registro
2. **Después del login**: Middleware `guest` redirige automáticamente al dashboard
3. **Intentos de navegación**: JavaScript previene uso de botones del navegador

### Flujo de Usuario Autenticado
1. **Acceso al dashboard**: Usuario autenticado puede navegar libremente
2. **Sesión activa**: Middleware actualiza timestamp en cada request
3. **Inactividad**: Después de 30 minutos, sesión expira automáticamente
4. **Intentos de acceso no autorizado**: Middleware `auth` redirige al login

### Expiración de Sesión
1. **Detección**: Middleware verifica tiempo desde última actividad
2. **Logout automático**: Si > 30 minutos, cierra sesión
3. **Redirección**: Envía al login con mensaje explicativo
4. **Limpieza**: Invalida sesión y regenera token CSRF

## ⚙️ Configuración Personalizada

### Cambiar Tiempo de Vida de Sesión
```bash
# Configurar para 60 minutos
php artisan session:configure-security --lifetime=60

# Aplicar cambios
php artisan config:clear
```

### Variables de Entorno
```env
SESSION_DRIVER=database
SESSION_LIFETIME=30
SESSION_EXPIRE_ON_CLOSE=false
SESSION_ENCRYPT=false
```

## 🔧 Comandos Útiles

```bash
# Configurar seguridad de sesiones
php artisan session:configure-security --lifetime=30

# Limpiar cachés después de cambios
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Ver rutas registradas
php artisan route:list
```

## 📋 Rutas Protegidas

### Solo para Usuarios No Autenticados (guest)
- `GET /` - Página de bienvenida
- `GET /iniciar-sesion` - Formulario de login
- `POST /iniciar-sesion` - Procesar login
- `GET /registro` - Formulario de registro
- `POST /registro` - Procesar registro
- `GET /recuperar-password` - Solicitar reset de contraseña

### Solo para Usuarios Autenticados (auth)
- `GET /dashboard` - Dashboard administrativo
- `GET /dashboard2` - Dashboard de solicitante
- `GET /users` - Gestión de usuarios
- `GET /roles` - Gestión de roles
- `GET /proveedores` - Gestión de proveedores
- `GET /tramites` - Gestión de trámites

## 🛡️ Beneficios de Seguridad

1. **Prevención de acceso no autorizado** mediante middlewares
2. **Protección contra sesiones infinitas** con expiración automática
3. **Bloqueo de navegación manual** con JavaScript y headers
4. **Invalidación segura de sesiones** al cerrar o expirar
5. **Mensajes informativos** para mejor experiencia de usuario

## 🔍 Debugging

Para verificar el estado de las sesiones:
```php
// En cualquier controlador
Log::info('Sesión actual', [
    'user_id' => Auth::id(),
    'last_activity' => session('last_activity'),
    'session_id' => session()->getId()
]);
```

## ⚠️ Consideraciones

- Los headers anti-cache pueden afectar el rendimiento en páginas con mucho contenido estático
- El JavaScript de anti-navegación no funciona si el usuario tiene JavaScript deshabilitado
- La expiración de sesión es más estricta que el comportamiento estándar de Laravel
- Los usuarios necesitarán reautenticarse más frecuentemente 