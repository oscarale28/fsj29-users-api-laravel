# Use official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules and configure DocumentRoot
RUN a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set Apache DocumentRoot to public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy entrypoint script first (changes less frequently)
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy composer files for better Docker layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies (without dev dependencies for production)
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction

# Copy application files
COPY . /var/www/html/

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Health check - using Laravel's health endpoint
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

# Use entrypoint script for Laravel initialization
CMD ["/usr/local/bin/docker-entrypoint.sh"]