#!/bin/sh

set -e

echo "🚀 Iniciando contenedor Laravel API..."

# Crear directorios necesarios
mkdir -p /var/www/html/storage/framework/{cache,sessions,views}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Asignar permisos correctos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Esperar a que la BD esté lista (solo si no es SQLite)
if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "⏳ Esperando a que la base de datos ($DB_HOST:$DB_PORT) esté disponible..."
    until nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
        echo "⏳ Esperando conexión a la BD..."
        sleep 2
    done
    echo "✅ Base de datos disponible!"
fi

# Crear base SQLite si aplica
if [ "$DB_CONNECTION" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$DB_PATH" ]; then
        echo "📦 Creando base SQLite en $DB_PATH"
        touch "$DB_PATH"
        chown www-data:www-data "$DB_PATH"
        chmod 664 "$DB_PATH"
    fi
fi

# Limpiar y cachear configuración
echo "⚙️  Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "⚙️  Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones (si se desea)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "🔄 Ejecutando migraciones..."
    php artisan migrate --force --no-interaction || true
fi

# Generar documentación Swagger si existe el comando
if [ -f artisan ]; then
    echo "📘 Generando documentación Swagger..."
    php artisan l5-swagger:generate || echo "⚠️ No se pudo generar Swagger"
fi

echo "✅ Contenedor listo. Iniciando servidor..."
exec "$@"
