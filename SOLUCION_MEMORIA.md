# Solución para Error de Memoria en AsentamientosSeeder

## Problema Identificado

El error `Allowed memory size of 536870912 bytes exhausted` ocurre porque:

1. **Archivo JSON muy grande**: El archivo `asentamientos.json` es mayor a 2MB
2. **Límite de memoria PHP**: El límite actual es de 128MB
3. **Procesamiento ineficiente**: El seeder original carga todo el archivo en memoria

## Soluciones Implementadas

### 1. Seeder Optimizado (`AsentamientosSeeder.php`)

**Mejoras implementadas:**
- ✅ Reducción del tamaño de chunk de 1000 a 500 registros
- ✅ Procesamiento por lotes de 1MB para archivos grandes
- ✅ Validación de registros antes de insertar
- ✅ Progreso visual durante la ejecución
- ✅ Manejo de errores mejorado

### 2. Configuración PHP Optimizada (`php-seeder.ini`)

**Configuraciones:**
```ini
memory_limit = 1G           # Aumenta límite a 1GB
max_execution_time = 1800   # 30 minutos
max_input_time = 1800       # 30 minutos
post_max_size = 1G          # 1GB
upload_max_filesize = 1G    # 1GB
```

### 3. Scripts de Ejecución

**PowerShell** (`run-seeder.ps1`):
```powershell
php -c php-seeder.ini artisan db:seed --class=AsentamientosSeeder
```

**Batch** (`run-seeder.bat`):
```batch
php -c php-seeder.ini artisan db:seed --class=AsentamientosSeeder
```

### 4. Seeder Ultra-Optimizado (`AsentamientosSeederOptimized.php`)

**Para archivos extremadamente grandes:**
- ✅ Procesamiento línea por línea
- ✅ Chunks de 250 registros
- ✅ Streaming JSON parsing
- ✅ Manejo de memoria ultra-eficiente

## Cómo Usar las Soluciones

### Opción 1: Seeder Optimizado (Recomendado)
```bash
# Usar el script de PowerShell
.\run-seeder.ps1

# O usar el script de Batch
run-seeder.bat

# O ejecutar manualmente
php -c php-seeder.ini artisan db:seed --class=AsentamientosSeeder
```

### Opción 2: Seeder Ultra-Optimizado (Para archivos muy grandes)
```bash
# Modificar DatabaseSeeder.php para usar la versión optimizada
php -c php-seeder.ini artisan db:seed --class=AsentamientosSeederOptimized
```

### Opción 3: Ejecutar solo el seeder específico
```bash
# Sin modificar DatabaseSeeder.php
php -c php-seeder.ini artisan db:seed --class=AsentamientosSeeder
```

## Verificación de la Solución

### 1. Verificar configuración PHP
```bash
php -c php-seeder.ini -i | findstr memory_limit
# Debe mostrar: memory_limit => 1G => 1G
```

### 2. Verificar tamaño del archivo
```bash
# El script mostrará el tamaño del archivo al ejecutarse
# Si es mayor a 10MB, mostrará una advertencia
```

### 3. Monitorear progreso
- El seeder mostrará progreso cada 1000 registros
- Mostrará el total de registros procesados al final

## Solución de Problemas

### Error: "No se pudo abrir el archivo JSON"
- Verificar que `public/json/asentamientos.json` existe
- Verificar permisos de lectura

### Error: "Formato de archivo JSON inválido"
- Verificar que el JSON es válido
- Usar la versión ultra-optimizada para archivos corruptos

### Error: "Allowed memory size exhausted"
- Asegurar que se está usando `php-seeder.ini`
- Reducir `chunkSize` en el seeder
- Usar la versión ultra-optimizada

### Error: "Timeout"
- Aumentar `max_execution_time` en `php-seeder.ini`
- Procesar en archivos más pequeños

## Recomendaciones Adicionales

### 1. Para Producción
- Considerar dividir el archivo JSON en archivos más pequeños
- Usar un proceso en background para seeders grandes
- Implementar un sistema de progreso persistente

### 2. Para Desarrollo
- Usar datasets más pequeños para desarrollo
- Implementar un flag para saltar seeders grandes en desarrollo

### 3. Optimización de Base de Datos
- Deshabilitar índices durante la inserción masiva
- Usar `DB::disableQueryLog()` para reducir uso de memoria
- Considerar usar `LOAD DATA INFILE` para archivos muy grandes

## Archivos Modificados/Creados

1. `database/seeders/AsentamientosSeeder.php` - Optimizado
2. `database/seeders/AsentamientosSeederOptimized.php` - Ultra-optimizado
3. `php-seeder.ini` - Configuración PHP optimizada
4. `run-seeder.ps1` - Script PowerShell
5. `run-seeder.bat` - Script Batch
6. `SOLUCION_MEMORIA.md` - Esta documentación

## Próximos Pasos

1. Ejecutar el seeder optimizado
2. Verificar que todos los asentamientos se insertaron correctamente
3. Considerar implementar la versión ultra-optimizada si persisten problemas
4. Documentar el proceso para el equipo de desarrollo 