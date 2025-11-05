#!/bin/bash
set -e

echo "🚀 Iniciando contenedor de Laravel..."

# Verificar si el .env existe
if [ ! -f .env ]; then
    echo "⚙️  No existe .env, creando desde ejemplo..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "APP_KEY=" > .env
    fi
fi

# Si existe variable APP_KEY en entorno, actualizar .env
if [ -n "$APP_KEY" ]; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|g" .env
fi

# Esperar a MySQL (si existe variable DB_HOST)
if [ -n "$DB_HOST" ]; then
    echo "⏳ Esperando a que MySQL ($DB_HOST) esté disponible..."
    until nc -z -v -w30 $DB_HOST ${DB_PORT:-3306}; do
      echo "   → Esperando a MySQL..."
      sleep 3
    done
    echo "✅ MySQL disponible!"
fi

# Limpiar y optimizar Laravel
echo "⚙️  Limpiando cachés..."
php artisan config:clear || true
php artisan cache:clear || true

# Solo generar key si no existe ya en el .env
if ! grep -q "APP_KEY=base64" .env; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --ansi || true
fi

echo "⚙️  Cacheando configuración, rutas y vistas..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Contenedor listo. Iniciando Apache..."
exec apache2-foreground
