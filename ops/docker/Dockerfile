# syntax=docker/dockerfile:1

# ──────────────────────────────────────────────
# Stage 1 — builder: install Composer dependencies
# ──────────────────────────────────────────────
FROM php:8.4-fpm-alpine AS builder

WORKDIR /app

# Install system dependencies required by PHP extensions
RUN apk add --no-cache \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        icu-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
        intl \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy dependency manifests first (layer cache)
COPY composer.json composer.lock symfony.lock ./

# ──────────────────────────────────────────────
# Dev target — installs all dependencies (including dev)
# ──────────────────────────────────────────────
FROM builder AS dev-deps

RUN composer install \
        --no-scripts \
        --no-interaction \
        --prefer-dist

# ──────────────────────────────────────────────
# Prod target — no dev dependencies
# ──────────────────────────────────────────────
FROM builder AS prod-deps

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader

# ──────────────────────────────────────────────
# Stage 2 — runtime base
# ──────────────────────────────────────────────
FROM php:8.4-fpm-alpine AS runtime

WORKDIR /var/www/html

# System deps for runtime only; -dev packages are needed to compile extensions
# and removed afterward to keep the image small
RUN apk add --no-cache \
        libpq \
        libzip \
        icu \
    && apk add --no-cache --virtual .build-deps \
        libpq-dev \
        libzip-dev \
        icu-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
        intl \
        opcache \
    && apk del .build-deps

# Copy custom PHP and FPM config
COPY ops/docker/php/php.ini     /usr/local/etc/php/conf.d/app.ini
COPY ops/docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Set non-root user
RUN chown -R www-data:www-data /var/www/html
USER www-data

# ──────────────────────────────────────────────
# Dev image
# ──────────────────────────────────────────────
FROM runtime AS dev

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data --from=dev-deps /app/vendor ./vendor
COPY --chown=www-data:www-data . .

RUN php bin/console cache:warmup --env=dev 2>/dev/null || true

EXPOSE 9000
CMD ["php-fpm"]

# ──────────────────────────────────────────────
# Production image
# ──────────────────────────────────────────────
FROM runtime AS prod

COPY --chown=www-data:www-data --from=prod-deps /app/vendor ./vendor
COPY --chown=www-data:www-data . .

RUN php bin/console cache:warmup --env=prod

EXPOSE 9000
CMD ["php-fpm"]
