FROM php:8.1-fpm

RUN printf "\n \n" | pecl install redis && docker-php-ext-enable redis
RUN apt-get update \
    && apt-get install -y zlib1g-dev g++ libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/weather_app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer