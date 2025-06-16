FROM php:8.3-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql zip

# Activar módulos de Apache
RUN a2enmod rewrite

# Configurar PHP para desarrollo
RUN echo "php_flag opcache.enable Off" >> /etc/apache2/conf-enabled/docker-php.conf

WORKDIR /var/www/html