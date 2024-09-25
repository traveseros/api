# Usar PHP 8.2 como base
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install intl opcache pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos de la aplicación al contenedor
COPY . .

# Instalar dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Exponer el puerto 9000
EXPOSE 8888

# Iniciar PHP-FPM
CMD ["php-fpm"]
