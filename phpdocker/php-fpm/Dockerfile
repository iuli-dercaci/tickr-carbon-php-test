FROM php:7.4-fpm-alpine

WORKDIR "/application"

RUN apk update
RUN apk add libmcrypt-dev openssl
RUN docker-php-ext-install pdo

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 8080

CMD [ "php", "-S", "0.0.0.0:8080", "-t", "public" ]