# 🚀 SOLUCIÓN COMPLETA PARA UPLOADS GRANDES (Error 413)

## 📋 Problema
Error HTTP 413 "Content Too Large" al subir documentos grandes (> 8MB aproximadamente) en la aplicación Laravel.

## 🎯 Solución Implementada

### 1. Configuración del Servidor Web (Apache)

**Archivo modificado:** `public/.htaccess`
```apache
# Límite de Apache para el tamaño del cuerpo de la petición (110MB)
LimitRequestBody 115343360

# Configuraciones PHP
php_value upload_max_filesize 100M
php_value post_max_size 110M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 512M
php_value max_file_uploads 20
```

### 2. Base de Datos - Campos Faltantes

**Nueva migración:** `database/migrations/2025_12_26_120000_add_missing_fields_to_documento_solicitante_table.php`

Agrega campos faltantes:
- `nombre_original` - Para almacenar el nombre original del archivo
- `comentarios` - Para comentarios adicionales
- `archivo` - Para datos binarios si es necesario
- `documento_cotejado` - Para marcar documentos verificados físicamente

### 3. Controlador Optimizado

**Archivo mejorado:** `app/Http/Controllers/LocalDocumentoController.php`

Mejoras implementadas:
- Verificación temprana del tamaño del archivo
- Manejo específico de `PostTooLargeException`
- Logging detallado para debugging
- Mejor manejo de errores con códigos HTTP apropiados

### 4. Configuración de Storage

**Nuevo archivo:** `storage/app/public/.htaccess`
```apache
# Configuración para storage de documentos grandes
LimitRequestBody 115343360
php_value upload_max_filesize 100M
php_value post_max_size 110M
php_value memory_limit 512M
```

### 5. Middleware para Uploads

**Archivo existente:** `app/Http/Middleware/HandleLargeUploads.php`
- Ya configurado para manejar archivos grandes
- Logging detallado del proceso
- Configuración dinámica de límites PHP

## 🛠️ Instalación y Configuración

### Opción A: Script Automático (Recomendado)

1. **Ejecutar script de PowerShell (Windows):**
   ```powershell
   PowerShell -ExecutionPolicy Bypass -File configurar-uploads-grandes.ps1
   ```

2. **O ejecutar script de verificación:**
   ```bash
   php verificar-upload.php
   ```

### Opción B: Configuración Manual

1. **Ejecutar migración:**
   ```bash
   php artisan migrate
   ```

2. **Crear symlink de storage:**
   ```bash
   php artisan storage:link
   ```

3. **Limpiar caché:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Reiniciar servidor web** (Apache/Nginx/IIS)

## 📊 Verificación de la Configuración

Ejecutar el script de verificación:
```bash
php verificar-upload.php
```

Este script verificará:
- ✅ Configuración PHP actual
- ✅ Archivos de configuración existentes
- ✅ Permisos de directorios
- ✅ Estado de migraciones
- ✅ Symlink de storage

## 🔍 Archivos Modificados/Creados

### Archivos Modificados:
1. `public/.htaccess` - Configuración Apache
2. `app/Http/Controllers/LocalDocumentoController.php` - Controlador optimizado

### Archivos Creados:
1. `database/migrations/2025_12_26_120000_add_missing_fields_to_documento_solicitante_table.php`
2. `storage/app/public/.htaccess`
3. `verificar-upload.php`
4. `configurar-uploads-grandes.ps1`
5. `README_SOLUCION_UPLOADS_GRANDES.md`

### Archivos Existentes (No modificados):
- `php-dev.ini` - Ya configurado correctamente
- `app/Http/Middleware/HandleLargeUploads.php` - Ya existía y funcionaba
- `app/Http/Kernel.php` - Middleware ya registrado

## 🎯 Límites Configurados

| Configuración | Valor | Descripción |
|---------------|-------|-------------|
| `upload_max_filesize` | 100M | Tamaño máximo por archivo |
| `post_max_size` | 110M | Tamaño máximo POST total |
| `LimitRequestBody` | 115343360 bytes | Límite Apache (110MB) |
| `memory_limit` | 512M | Memoria PHP |
| `max_execution_time` | 300s | Tiempo máximo de ejecución |

## 🚨 Troubleshooting

### Si el problema persiste:

1. **Verificar logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verificar logs del servidor web:**
   - Apache: `/var/log/apache2/error.log` (Linux) o Event Viewer (Windows)
   - Nginx: `/var/log/nginx/error.log`

3. **Probar con archivo pequeño primero:**
   - Subir archivo < 10MB para confirmar que funciona

4. **Verificar configuración actual:**
   ```bash
   php -i | grep -E "(upload_max_filesize|post_max_size|memory_limit)"
   ```

5. **Verificar que no hay proxy/CDN limitando uploads**

### Errores Comunes:

| Error | Causa | Solución |
|-------|-------|----------|
| HTTP 413 | Límite del servidor web | Verificar `LimitRequestBody` en .htaccess |
| HTTP 422 | Validación Laravel | Verificar límites en validación |
| Timeout | Archivo muy grande o lento | Aumentar `max_execution_time` |
| Out of Memory | Poca memoria | Aumentar `memory_limit` |

## 🔒 Seguridad

La configuración incluye medidas de seguridad:
- Solo archivos PDF permitidos
- Validación de tipos MIME
- Headers de seguridad para PDFs
- Prevención de ejecución de scripts en storage

## 📞 Soporte

Si necesitas ayuda adicional:
1. Ejecuta `php verificar-upload.php` y comparte el resultado
2. Revisa los logs de Laravel y del servidor web
3. Verifica que todos los archivos de configuración estén en su lugar

---

**✨ Con esta configuración, la aplicación puede manejar archivos PDF de hasta 100MB sin problemas.** 