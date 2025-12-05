# Stage 1: Build frontend assets (Laravel Mix / Vite)
FROM node:18 AS node_builder
WORKDIR /app

COPY package*.json ./
RUN npm install --silent

COPY . .
RUN npm run build || npm run production || true


# Stage 2: PHP 8.2 + Composer + Extensions
FROM php:8.1-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev libgmp-dev pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd intl zip gmp \
    && docker-php-ext-install fileinfo ctype \
    && pecl install redis && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_MEMORY_LIMIT=-1

WORKDIR /var/www/html

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install PHP dependencies without running project scripts (we'll run them after copying app)
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts -vvv

# Copy app source code

# Copy application source
COPY . .

# Ensure an environment file exists for artisan commands that expect it
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Run composer and artisan tasks that were deferred (package discovery, optimized autoload)
RUN composer dump-autoload --optimize || true
RUN php artisan package:discover --ansi || true

# Copy compiled frontend assets
COPY --from=node_builder /app/public /var/www/html/public

# Laravel optimizations
RUN php artisan key:generate --force || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache vendor public

# Run as www-data user
USER www-data

EXPOSE 9000

CMD ["php-fpm"]
