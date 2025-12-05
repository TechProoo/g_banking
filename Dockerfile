# Multi-stage Dockerfile for Laravel + Laravel Mix

# Stage 1 - Node builder: compile frontend assets with Laravel Mix
FROM node:18 AS node_builder
WORKDIR /app
COPY package*.json ./
COPY package-lock.json ./
RUN npm ci --silent
COPY . .
# Use the production script that runs `mix --production`
RUN npm run production

# Stage 2 - PHP application
FROM php:8.2-fpm AS app

# Install system dependencies required for common PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    zip \
    ca-certificates \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache curl \
    && pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer manifest first to leverage Docker cache
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
# Configure composer cache dir to a path that will be cached by Docker layers when possible
ENV COMPOSER_CACHE_DIR=/var/cache/composer
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative --prefer-dist --no-interaction --no-progress --no-suggest && composer clear-cache

# Copy application source
COPY . .

# Copy compiled frontend assets from node_builder
# Laravel Mix outputs to `public/js` and `public/css` by default
COPY --from=node_builder /app/public /var/www/html/public

# Optimize Laravel (best effort; will not fail the build if commands error)
RUN php artisan key:generate --force || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Set folder permissions for runtime and switch to non-root user
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/vendor /var/www/html/public || true

# Run container as the standard www-data user
USER www-data

EXPOSE 9000

# Simple healthcheck: verify php-fpm is running in the container
HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 CMD pgrep php-fpm || exit 1

CMD ["php-fpm"]
