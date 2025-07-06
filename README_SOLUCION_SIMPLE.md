# 🎯 Solución Simple: Solo `artisan serve` + Storage Local

## ✅ ¿Qué es esto?

Una solución **super simple** para subir archivos grandes **sin configuraciones complicadas** de servidor. Solo usa:

- `php artisan serve` (servidor normal de Laravel)
- Almacenamiento local en `storage/app/public/`
- Middleware que ajusta límites automáticamente
- Sin archivos PHP.ini especiales
- Sin scripts complicados

## 🚀 Cómo Usar (Súper Fácil)

### 1. Ejecutar el Script Simple

```powershell
.\start-simple.ps1
```

**¡Eso es todo!** 🎉

### 2. ¿Qué hace el script?

```powershell
# 1. Crea enlace de storage
php artisan storage:link

# 2. Limpia cache
php artisan config:clear

# 3. Crea directorios necesarios
# 4. Inicia servidor normal
php artisan serve --port=8000
```

## 📁 ¿Dónde se Guardan los Archivos?

Los archivos se guardan en:
```
storage/app/public/documentos_solicitante/
  ├── 1/  (trámite ID 1)
  │   ├── 1635789123_5_documento.pdf
  │   └── 1635789456_3_constancia.pdf
  ├── 2/  (trámite ID 2)
  └── 3/  (trámite ID 3)
```

Accesibles via web en:
```
http://127.0.0.1:8000/storage/documentos_solicitante/1/archivo.pdf
```

## 🔧 ¿Cómo Funciona la Magia?

### 1. Middleware Inteligente

Se creó `HandleLargeUploads` que automáticamente detecta cuando subes un archivo y ajusta los límites:

```php
// Se activa automáticamente cuando detecta un archivo
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
ini_set('memory_limit', '512M');
```

### 2. Controlador Local Simple

`LocalDocumentoController` que:
- ✅ Guarda archivos directamente en `storage/app/public/`
- ✅ No encripta rutas (más simple)
- ✅ Usa nombres de archivo con timestamp
- ✅ Retorna URLs directas accesibles

### 3. Rutas Locales

Nuevas rutas que no dependen de configuraciones:
- `/tramites-solicitante/upload-documento-local`
- `/tramites-solicitante/documentos-local`
- `/tramites-solicitante/ver-documento-local/{tramite}/{documento}`

## 💯 Ventajas de Esta Solución

| ✅ Ventaja | 🔴 Antes | 🟢 Ahora |
|------------|----------|----------|
| **Configuración** | PHP.ini personalizado | Solo middleware |
| **Servidor** | Scripts especiales | `artisan serve` normal |
| **Archivos** | Rutas encriptadas | Rutas directas |
| **Acceso** | Complejo | URLs simples |
| **Desarrollo** | Configuración manual | Automático |

## 🔍 Verificar que Funciona

1. **Ver archivos guardados:**
   ```
   ls storage/app/public/documentos_solicitante/
   ```

2. **Acceder via web:**
   ```
   http://127.0.0.1:8000/storage/documentos_solicitante/1/archivo.pdf
   ```

3. **Logs de subida:**
   ```
   tail -f storage/logs/laravel.log | grep "Documento subido localmente"
   ```

## 🚫 Ya NO Necesitas

- ❌ `php-dev.ini`
- ❌ `start-dev.ps1` complejo
- ❌ Configuraciones de servidor web
- ❌ Encriptación de rutas
- ❌ Scripts de verificación

## 🔄 Migrar de la Solución Anterior

Si ya usabas la solución compleja:

1. **Detén el servidor actual**
2. **Ejecuta el script simple:**
   ```powershell
   .\start-simple.ps1
   ```
3. **Los archivos existentes seguirán funcionando**

## 🛠️ Troubleshooting Simple

### Error 413 persiste:
```powershell
# Solo reinicia el servidor
# El middleware se encarga del resto
.\start-simple.ps1
```

### No se guardan archivos:
```powershell
# Verificar permisos
php artisan storage:link
```

### No se ven archivos:
```
# Verificar en:
storage/app/public/documentos_solicitante/
```

## 🎉 ¡Es Así de Simple!

1. `.\start-simple.ps1`
2. Ve a tu app
3. Sube archivos hasta 100MB
4. **¡Funciona!** 🚀

No más configuraciones complicadas. Solo Laravel haciendo su magia. ✨ 