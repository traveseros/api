# Usar una imagen base de PHP con las extensiones necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev

# Instalar extensiones de PHP necesarias para Symfony
RUN docker-php-ext-install intl pdo pdo_pgsql

# Instalar Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto
COPY . /var/www/html/

# Ejecutar composer install
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Exponer el puerto 9000 y ejecutar el servidor PHP-FPM
#EXPOSE 9000
CMD ["php-fpm"]
