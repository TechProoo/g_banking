# Stage 1: Build frontend assets (if using Laravel Mix/Vite)
FROM node:18 AS node_builder
WORKDIR /app

COPY package*.json ./
RUN npm install --silent

COPY . .
RUN npm run build || npm run production || true



# Stage 2: PHP + Composer + Extensions
FROM php:8.2-fpm

# Install system dependencies & required PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev libgmp-dev libmagickwand-dev pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd intl zip \
    && docker-php-ext-install fileinfo ctype tokenizer \
    && pecl install redis && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Increase memory for Composer
ENV COMPOSER_MEMORY_LIMIT=-1

WORKDIR /var/www/html

# Copy composer files first (cache layers)
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction -vvv

# Copy app source
COPY . .

# Copy built frontend assets
COPY --from=node_builder /app/public /var/www/html/public

# Generate Laravel optimizations (ignore errors)
RUN php artisan key:generate --force || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
