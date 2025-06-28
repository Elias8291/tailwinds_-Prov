# Despliegue en Railway 🚀

Este proyecto Laravel está configurado para desplegarse en [Railway](https://railway.com/) de manera simple, sin nixpacks ni Docker.

## 📋 Configuración Realizada

### ✅ Archivos Creados/Modificados:
- ❌ **Eliminado**: `nixpacks.toml` (como solicitado)
- ❌ **Eliminado**: `Dockerfile` y `.dockerignore` (configuración Docker removida)
- ✅ **Actualizado**: `railway.json` - Configuración simple y directa
- ✅ **Creado**: `railway_build.sh` - Script de build personalizado
- ✅ **Creado**: `start.sh` - Script de inicio simple
- ✅ **Creado**: `Procfile` - Configuración de proceso para Railway

## 🚀 Pasos para Desplegar en Railway

### 1. Subir el Código
```bash
git add .
git commit -m "Configuración para Railway sin nixpacks"
git push origin main
```

### 2. Crear Proyecto en Railway
1. Ve a [railway.com](https://railway.com/)
2. Haz clic en "New Project"
3. Selecciona "Deploy from GitHub repo"
4. Selecciona tu repositorio

### 3. Variables de Entorno Requeridas
En el dashboard de Railway, configura estas variables:

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:XXXXX (se generará automáticamente)
APP_DEBUG=false
APP_URL=https://tu-dominio.up.railway.app

DB_CONNECTION=mysql
DB_HOST=tu-host-db
DB_PORT=3306
DB_DATABASE=tu-database
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password

# Opcional para ejecutar migraciones automáticamente
RUN_MIGRATIONS=true
```

### 4. Base de Datos
Railway detectará que necesitas una base de datos. Puedes:
- Agregar MySQL/PostgreSQL desde el dashboard
- Las variables de conexión se configurarán automáticamente

### 5. Dominio Personalizado (Opcional)
1. En el dashboard, ve a "Settings"
2. En "Domains", agrega tu dominio personalizado

## 🔧 Características de la Configuración

### Configuración Ultra Simple
- Sin Docker, sin nixpacks - configuración directa
- Railway detecta automáticamente que es un proyecto PHP
- Build command personalizado para dependencias y assets
- Procfile para definir cómo iniciar la aplicación
- Servidor PHP integrado de Laravel

### Scripts Personalizados
- **railway_build.sh**: Instala dependencias y builda assets
- **start.sh**: Configura y arranca Laravel
- **Procfile**: Define el proceso web
- Optimizaciones automáticas para producción

### Railway.json Minimalista
- Solo build command personalizado
- Healthcheck en la ruta raíz
- Configuración básica de replicas y reinicio
- Railway maneja todo lo demás automáticamente

## 📊 Monitoreo

Una vez desplegado, puedes monitorear tu aplicación en:
- **Logs**: Ver logs en tiempo real en el dashboard
- **Metrics**: CPU, memoria y tráfico
- **Deployments**: Historial de despliegues

## 🐛 Troubleshooting

### Si el build falla:
1. Revisa los logs en Railway dashboard
2. Verifica que todas las dependencias estén en `composer.json`
3. Asegúrate de que el `APP_KEY` esté configurado

### Si la aplicación no inicia:
1. Revisa las variables de entorno
2. Verifica la configuración de la base de datos
3. Revisa los logs de la aplicación

## 📝 Comandos Útiles

Para desarrollo local:
```bash
# Instalar dependencias
composer install
npm ci

# Ejecutar build de assets
npm run build

# Iniciar servidor local
php artisan serve
```

---

¡Tu aplicación Laravel está lista para desplegarse en Railway de forma simple! 🎉 