# PHP 8.3 FPM
FROM php:8.3-fpm

# Install package Linux
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Install extension PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Folder kerja
WORKDIR /var/www

# Copy source code frontend
COPY . .

# Install dependency Laravel
RUN composer install

# Permission Laravel
RUN chmod -R 775 storage bootstrap/cache

# PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]