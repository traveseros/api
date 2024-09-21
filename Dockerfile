# Etapa de construcción
FROM php:8.2-fpm AS build

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install intl opcache pdo pdo_pgsql zip

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Añadir /usr/local/bin al PATH
ENV PATH="/usr/local/bin:$PATH"

# Verificar la instalación de Composer
RUN composer --version

# Instalar Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Crear un usuario no-root para ejecutar los comandos
RUN useradd -m -u 1000 symfony
USER symfony

# Copiar los archivos de la aplicación
COPY --chown=symfony:symfony . /var/www/html

# Instalar dependencias de Composer sin entorno de desarrollo
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Etapa de producción
FROM php:8.2-fpm AS production

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install intl opcache pdo pdo_pgsql zip

# Crear un usuario no-root para ejecutar la aplicación
RUN useradd -m -u 1000 symfony
USER symfony

# Copiar la aplicación desde la etapa de construcción
COPY --from=build /var/www/html /var/www/html

# Exponer el puerto 9000
EXPOSE 9000

# Ejecutar el servidor PHP-FPM
CMD ["php-fpm"]
