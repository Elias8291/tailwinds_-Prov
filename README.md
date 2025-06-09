<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Sistema de Registro de Proveedores con SAT

Este sistema permite el registro automatizado de proveedores utilizando la Constancia de Situación Fiscal del SAT con código QR.

## Características Implementadas

### Registro con SAT Scraper
- **Lectura de QR**: Extrae datos automáticamente de la Constancia de Situación Fiscal
- **Validación de datos**: Verifica la información obtenida del SAT
- **Registro completo**: Crea todas las entidades relacionadas en una sola transacción

### Flujo de Registro

1. **Subida de documento**: El usuario sube su Constancia de Situación Fiscal (PDF/Imagen)
2. **Lectura de QR**: El sistema lee el código QR y extrae la URL del SAT
3. **Scraping de datos**: Se obtienen los datos fiscales desde el sitio del SAT
4. **Formulario completado**: Los datos se llenan automáticamente
5. **Registro en BD**: Se crean todos los registros relacionados

### Estructura de Datos Creada

#### 1. Dirección
```php
Direccion::create([
    'codigo_postal' => $satData['cp'],
    'asentamiento_id' => null,  // Se puede buscar por CP
    'calle' => $satData['nombreVialidad'],
    'numero_exterior' => $satData['numeroExterior'],
    'numero_interior' => $satData['numeroInterior'],
]);
```

#### 2. Usuario
```php
User::create([
    'nombre' => $nombreUsuario,  // Razón social si es moral, nombre completo si es física
    'correo' => $request->email,
    'rfc' => $satData['rfc'],
    'password' => Hash::make($request->password),
    'estado' => 'pendiente',
    'verification_token' => $verificationToken,
]);
```

#### 3. Solicitante
```php
Solicitante::create([
    'usuario_id' => $user->id,
    'tipo_persona' => $satData['tipo_persona'],
    'curp' => $satData['tipo_persona'] === 'Física' ? $satData['curp'] : null,
    'rfc' => $satData['rfc'],
]);
```

#### 4. Trámite
```php
Tramite::create([
    'solicitante_id' => $solicitante->id,
    'tipo_tramite' => 'Inscripcion',
    'estado' => 'Pendiente',
    'progreso_tramite' => 0,
    'fecha_inicio' => now(),
]);
```

#### 5. Detalle de Trámite
```php
DetalleTramite::create([
    'tramite_id' => $tramite->id,
    'razon_social' => $satData['nombre'],
    'email' => $request->email,
    'direccion_id' => $direccion->id,
]);
```

#### 6. Documento Solicitante
```php
DocumentoSolicitante::create([
    'tramite_id' => $tramite->id,
    'documento_id' => $documento->id,  // Constancia de Situación Fiscal
    'fecha_entrega' => now(),
    'estado' => 'Aprobado',
    'version_documento' => 1,
    'ruta_archivo' => $rutaEncriptada,
]);
```

## Instalación y Configuración

### 1. Ejecutar Migraciones
```bash
php artisan migrate
```

### 2. Ejecutar Seeders
```bash
php artisan db:seed
```

### 3. Crear directorio de almacenamiento
```bash
php artisan storage:link
mkdir -p storage/app/public/documentos_solicitante
```

### 4. Configurar permisos
```bash
chmod -R 755 storage/app/public/documentos_solicitante
```

## Validaciones Implementadas

### Frontend (JavaScript)
- Validación de tipo de archivo (PDF, PNG, JPG, JPEG)
- Validación de tamaño máximo (5MB)
- Verificación de código QR válido
- Validación de datos extraídos del SAT

### Backend (Laravel)
- Validación de correo único
- Validación de RFC del SAT
- Validación de tipo de persona
- Validación de archivo subido
- Transacciones de base de datos

## Archivos Modificados/Creados

### Controlador Principal
- `app/Http/Controllers/Auth/RegisterController.php`

### Vistas
- `resources/views/auth/register.blade.php`

### Modelos Actualizados
- `app/Models/DocumentoSolicitante.php`

### JavaScript del SAT Scraper
- `public/js/scrapers/sat-scraper.js`

## Campos Ocultos del Formulario

El formulario incluye campos ocultos que se llenan automáticamente:

```html
<input type="hidden" id="qrUrl" name="qr_url">
<input type="hidden" id="satRfc" name="sat_rfc">
<input type="hidden" id="satNombre" name="sat_nombre">
<input type="hidden" id="satTipoPersona" name="sat_tipo_persona">
<input type="hidden" id="satCurp" name="sat_curp">
<input type="hidden" id="satCp" name="sat_cp">
<input type="hidden" id="satColonia" name="sat_colonia">
<input type="hidden" id="satNombreVialidad" name="sat_nombre_vialidad">
<input type="hidden" id="satNumeroExterior" name="sat_numero_exterior">
<input type="hidden" id="satNumeroInterior" name="sat_numero_interior">
```

## Logs y Debugging

El sistema incluye logging extensivo para facilitar el debugging:

```php
Log::info('Iniciando proceso de registro');
Log::info('Dirección creada', ['direccion_id' => $direccion->id]);
Log::info('Usuario creado', ['user_id' => $user->id]);
Log::info('Solicitante creado', ['solicitante_id' => $solicitante->id]);
Log::info('Trámite creado', ['tramite_id' => $tramite->id]);
Log::info('Detalle de trámite creado', ['detalle_tramite_id' => $detalleTramite->id]);
Log::info('Documento procesado y almacenado');
```

## Consideraciones de Seguridad

1. **Encriptación de rutas**: Las rutas de archivos se almacenan encriptadas
2. **Validación estricta**: Todos los datos se validan tanto en frontend como backend
3. **Transacciones**: Todo el proceso se ejecuta en una transacción de BD
4. **Tokens de verificación**: Se generan tokens únicos para verificación de email

## Próximos Pasos

1. Implementar verificación de email
2. Agregar búsqueda de asentamiento por código postal
3. Implementar notificaciones por email
4. Agregar dashboard para el solicitante
5. Implementar sistema de roles y permisos completo
