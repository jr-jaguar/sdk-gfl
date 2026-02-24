FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    autoconf \
    make \
    g++ \
    linux-headers

RUN docker-php-ext-install zip intl bcmath sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

COPY composer.json composer.lock* ./
COPY lib/ ./lib/

RUN composer install --no-interaction --no-scripts --no-autoloader --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["vendor/bin/phpunit"]
