FROM php:8.4-cli-alpine

# System deps for building the MongoDB PECL extension
RUN apk add --no-cache autoconf gcc g++ make

# Install mongodb PHP extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install dependencies first so Docker layer-caches them
COPY composer.json ./
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

COPY . .

EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
