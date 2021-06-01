FROM php:8.0.2-fpm-alpine
RUN apk --update --no-cache add git
RUN docker-php-ext-install pdo_mysql

COPY xdebug.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/symfony

