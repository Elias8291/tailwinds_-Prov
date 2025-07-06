# 🚀 Solución Error 413 - Content Too Large

## ❌ Problema
Error 413 (Content Too Large) al subir documentos que superen los límites del servidor.

## 🔍 Causa
El servidor PHP tiene límites configurados que son menores al tamaño de archivo que estás intentando subir:
- `upload_max_filesize` = (muy bajo o no configurado)
- `post_max_size` = 8M (actual) vs 60M (requerido)

## ✅ Solución Implementada

### 1. Configuraciones PHP Actualizadas

**Archivo: `php-dev.ini`**
```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

**Archivo: `public/.htaccess`**
```apache
php_value upload_max_filesize 100M
php_value post_max_size 110M
php_value memory_limit 512M
```

### 2. Controladores Actualizados

**TramiteSolicitanteController::subirDocumento()**
- Límite cambiado de 51200KB (50MB) a 102400KB (100MB)

**DocumentosController::validateRequest()**
- Límite cambiado de 51200KB (50MB) a 102400KB (100MB)

### 3. Frontend Actualizado

**seccion-documentos.blade.php**
- Validación JavaScript actualizada a 100MB
- Mensajes de error actualizados

## 🔧 Pasos para Aplicar la Solución

### Paso 1: Detener el Servidor Actual
```bash
# Presiona Ctrl+C en la terminal donde está corriendo el servidor
```

### Paso 2: Reiniciar con Nueva Configuración

**En Windows (PowerShell):**
```powershell
.\start-dev.ps1
```

**En Windows (CMD):**
```cmd
start-dev.bat
```

**En Linux/Mac:**
```bash
./start-dev.sh
```

### Paso 3: Verificar Configuración
El servidor debe mostrar:
```
✅ Iniciando servidor con configuración PHP optimizada para archivos grandes...
📁 Límites: upload_max_filesize=100M, post_max_size=100M
🌐 Servidor disponible en: http://127.0.0.1:8000
```

### Paso 4: Probar Subida de Archivos
1. Ve a la sección de documentos en el formulario
2. Intenta subir un archivo PDF entre 50MB-100MB
3. Debería funcionar correctamente sin error 413

## 🛠️ Script de Verificación

Puedes verificar que la configuración se aplicó correctamente visitando:
```
http://127.0.0.1:8000/php-info
```

O ejecutando en tu terminal:
```bash
php -c php-dev.ini -m | grep -i upload
```

## 📋 Límites Actuales

| Configuración | Valor Anterior | Valor Nuevo |
|---------------|----------------|-------------|
| `upload_max_filesize` | 50MB | **100MB** |
| `post_max_size` | 60MB | **100MB** |
| `memory_limit` | 256MB | **512MB** |
| `max_execution_time` | 300s | 300s |
| Validación Laravel | 51200KB | **102400KB** |
| Validación JavaScript | 50MB | **100MB** |

## 🔍 Troubleshooting

### Si el error 413 persiste:

1. **Verifica que el servidor se reinició:**
   ```bash
   # Detén completamente el servidor (Ctrl+C)
   # Luego ejecuta:
   .\start-dev.ps1
   ```

2. **Verifica la configuración de PHP:**
   ```bash
   php -c php-dev.ini -i | findstr upload_max_filesize
   ```

3. **Limpia cache del navegador:**
   - Presiona F12 > Application > Clear Storage

4. **Verifica el tamaño real del archivo:**
   - Asegúrate que el PDF sea menor a 100MB

### Si hay errores de servidor web (Apache/Nginx):

Puede ser necesario configurar límites adicionales en el servidor web:

**Apache (.htaccess ya actualizado):**
```apache
LimitRequestBody 104857600  # 100MB en bytes
```

**Nginx:**
```nginx
client_max_body_size 100M;
```

## 📞 Soporte

Si el problema persiste después de seguir estos pasos:

1. Verifica que el archivo `php-dev.ini` existe en el directorio raíz
2. Verifica que el script `start-dev.ps1` use el parámetro `-c php-dev.ini`
3. Revisa los logs de error de PHP en `storage/logs/laravel.log`
4. Verifica permisos de escritura en `storage/app/public/`

## ✅ Confirmación de Éxito

La solución está funcionando cuando:
- ✅ El servidor inicia con mensaje "Límites: upload_max_filesize=100M"
- ✅ Puedes subir archivos PDF de hasta 100MB sin error 413
- ✅ El formulario muestra "PDF, máximo 100MB" en la descripción
- ✅ Los logs no muestran errores de tamaño de archivo

## 📝 Archivos Frontend Actualizados

Los límites en JavaScript ya están configurados para 50MB:
- `resources/views/components/formularios/seccion-documentos.blade.php` ✅
- `resources/views/tramites/solicitante/constancia-fiscal.blade.php` ✅

## 🏭 Para Producción

Los archivos `.htaccess` y configuraciones de producción ya están listos en:
- `public/.htaccess` (Apache)
- `start.sh` (Railway/producción)

---

**💡 Tip**: Usa siempre los scripts `start-dev.*` para desarrollo local. 