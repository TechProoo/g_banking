# Stage 1: Build frontend assets (Laravel Mix / Vite)
FROM node:18 AS node_builder
WORKDIR /app

COPY package*.json ./
RUN npm install --silent

COPY . .
RUN npm run build || npm run production || true


# Stage 2: PHP 8.2 + Composer + Extensions
FROM php:8.1-apache

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev libgmp-dev pkg-config \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring bcmath gd intl zip gmp \
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

# Ensure an environment file exists for artisan commands that expect it
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Ensure Laravel cache directories exist and are writable before running artisan
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache || true

# Run composer and artisan tasks that were deferred (package discovery, optimized autoload)
RUN composer dump-autoload --optimize || true
RUN php artisan package:discover --ansi || true

# Copy static asset folders to public directory FIRST (before overwriting with node build)
RUN mkdir -p public && \
    cp -r dash public/dash 2>/dev/null || true && \
    cp -r dash2 public/dash2 2>/dev/null || true && \
    cp -r css public/css 2>/dev/null || true && \
    cp -r temp public/temp 2>/dev/null || true && \
    cp -r error public/error 2>/dev/null || true && \
    cp -r images public/images 2>/dev/null || true

# Copy compiled frontend assets from node builder (merge with existing public folder)
RUN --mount=type=bind,from=node_builder,source=/app/public,target=/tmp/node_public \
    cp -r /tmp/node_public/* public/ 2>/dev/null || true

# Laravel optimizations (skip caching - will be done at runtime with real env vars)
RUN php artisan key:generate --force || true

# enable common Apache modules
RUN a2enmod rewrite headers expires deflate

# If a `public/index.php` exists use it as document root, otherwise use project root
RUN if [ -f /var/www/html/public/index.php ]; then \
            sed -ri -e 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
            sed -ri -e 's!<Directory /var/www/html>!<Directory /var/www/html/public>!g' /etc/apache2/apache2.conf ; \
        else \
            sed -ri -e "s!DocumentRoot /var/www/html!DocumentRoot /var/www/html!g" /etc/apache2/sites-available/000-default.conf && \
            sed -ri -e "s!<Directory /var/www/html>!<Directory /var/www/html>!g" /etc/apache2/apache2.conf ; \
        fi

# Permissions (ensure apache can write where needed)
RUN chown -R www-data:www-data storage bootstrap/cache vendor public || true
RUN chmod -R 775 storage bootstrap/cache || true

# Copy and set up entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
