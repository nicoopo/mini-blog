FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpq-dev libicu-dev \
    && docker-php-ext-install pdo pdo_pgsql zip intl

RUN echo "date.timezone = Europe/Paris" > /usr/local/etc/php/conf.d/timezone.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
