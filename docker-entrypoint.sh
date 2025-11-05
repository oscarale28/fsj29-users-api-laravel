#!/bin/bash
set -e

echo "🚀 Iniciando API Laravel..."

# Esperar base de datos
if [ ! -z "$DB_HOST" ]; then
    echo "⏳ Esperando a la base de datos..."
    until nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
        echo "Esperando conexión con DB..."
        sleep 2
    done
    echo "✅ Base de datos lista"
fi

# Crear enlaces y limpiar caches
php artisan storage:link || true
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones (opcional)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "🔄 Ejecutando migraciones..."
    php artisan migrate --force
fi

echo "✅ Aplicación lista, iniciando Apache..."
exec apache2-foreground
