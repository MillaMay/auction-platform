FROM php:8.3-fpm-alpine

# Минимальные пакеты (ТОЛЬКО PostgreSQL)
RUN apk add --no-cache \
    git \
    curl \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev

# БАЗОВЫЕ расширения (без ошибок)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        zip \
        gd \
        bcmath

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
CMD ["php-fpm"]
