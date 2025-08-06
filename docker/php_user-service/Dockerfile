FROM php:8.2-fpm

RUN apt-get update -y && apt-get install -y librabbitmq-dev libzip-dev libicu-dev

RUN pecl install amqp && docker-php-ext-enable amqp

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install sockets

RUN apt-get update && apt-get install -y git curl zip

RUN docker-php-ext-install zip

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/user-service
