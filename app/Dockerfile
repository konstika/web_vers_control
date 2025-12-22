FROM php:8.2-apache

RUN a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

RUN apt-get update && apt-get install -y libzip-dev

RUN docker-php-ext-install pdo_mysql mysqli
RUN docker-php-ext-install zip
RUN pecl install redis \
    && docker-php-ext-enable redis

RUN chown -R www-data:www-data /var/www/html

COPY settings.ini /usr/local/etc/php/conf.d/settings.ini

EXPOSE 80
