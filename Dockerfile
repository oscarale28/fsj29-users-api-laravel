# Usa la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar módulos de Apache y configurar DocumentRoot
RUN a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar y dar permisos al entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copiar composer.json primero para aprovechar cache de Docker
COPY composer.json composer.lock ./

# Instalar dependencias PHP (sin dev)
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction

# Copiar todo el proyecto
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Exponer puerto
EXPOSE 80

# Ejecutar entrypoint
CMD ["/usr/local/bin/docker-entrypoint.sh"]
