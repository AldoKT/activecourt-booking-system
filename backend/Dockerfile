# Menggunakan PHP 8.3 FPM sebagai base image
FROM php:8.3-fpm

# Install dependency sistem
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Install extension PHP yang dibutuhkan Laravel
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Folder kerja di dalam container
WORKDIR /var/www

# Copy seluruh source code backend
COPY . .

# Install dependency Laravel
RUN composer install

# Permission storage & cache
RUN chmod -R 775 storage bootstrap/cache

# Port PHP-FPM
EXPOSE 9000

# Menjalankan PHP-FPM
CMD ["php-fpm"]