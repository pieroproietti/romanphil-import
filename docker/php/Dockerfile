FROM php:7-fpm-stretch
#FROM php:5.6.33-fpm-jessie

RUN apt-get update && apt-get install -y libpng-dev libcurl4-gnutls-dev

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install json \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install curl \
    && docker-php-ext-install gd

RUN chown www-data:www-data /var/www/html/ -R
