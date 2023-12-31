FROM composer:2 AS composer
WORKDIR /app

COPY composer.json composer.lock ./

# We need to ignore the platform requirements, because the Composer container
# does not come with some of the native extensions that our application requires.
RUN composer install --ignore-platform-reqs

FROM php:8.1-fpm AS api
WORKDIR /app

COPY --from=composer /app/vendor ./vendor

# Install library requirements
RUN apt update && apt install -y libcurl4-gnutls-dev libxml2-dev

# Grab pickle
RUN curl -Lo pickle.phar https://github.com/FriendsOfPHP/pickle/releases/download/v0.7.7/pickle.phar \
    && chmod +x pickle.phar \
    && mv pickle.phar /usr/local/bin/pickle

# Install required native extensions
RUN docker-php-ext-install -j$(nproc) curl dom fileinfo intl mysqli

# Install PHP Redis support
RUN pickle install -n igbinary
RUN pickle install -n redis
RUN docker-php-ext-enable redis

# Install XDebug for application profiling
RUN pickle install -n xdebug
COPY docker/90-xdebug.ini "$PHP_INI_DIR/conf.d"

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY writable ./writable
COPY public ./public
COPY app ./app

FROM api AS api-production
WORKDIR /app

# Link in the Docker secret
RUN ln -s /run/secrets/env ./.env

FROM api AS api-development
WORKDIR /app

# Copy the local development config
COPY .env ./
