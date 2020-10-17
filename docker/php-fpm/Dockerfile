FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libpq-dev git libicu-dev \
    && docker-php-ext-configure intl \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/symfony
